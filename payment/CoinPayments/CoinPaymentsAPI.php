<?php
namespace Sigismund\CoinPayments;

class CoinPaymentsAPI
{
    private $merchantID = '';
    private $ipnSecret = '';
    private $privateKey = '';
    private $public_key = '';
    private $ch = null;

    public function __construct($privateKey, $public_key, $merchantID, $ipnSecret)
    {
        $this->ipnSecret = $ipnSecret;
        $this->merchantID = $merchantID;
        $this->privateKey = $privateKey;
        $this->publicKey = $public_key;
        $this->ch = null;
    }

    /**
     * Gets the current CoinPayments.net exchange rate. Output includes both crypto and fiat currencies.
     * @param short If short == true (the default), the output won't include the currency names and confirms needed to save bandwidth.
     */
    public function getRates(bool $short = true, bool $accepted = true)
    {
        return $this->apiCall('rates', ['short' => (int) $short, 'accepted' => (int) $accepted]);
    }

    /**
     * Gets your current coin balances (only includes coins with a balance unless all = true).<br />
     * @param all If all = true then it will return all coins, even those with a 0 balance.
     */
    public function getBalances($all = false)
    {
        return $this->apiCall('balances', array('all' => $all ? 1:0));
    }

    /**
     * Creates a basic transaction with minimal parameters.<br />
     * See CreateTransaction for more advanced features.
     * @param amount The amount of the transaction (floating point to 8 decimals).
     * @param currency1 The source currency (ie. USD), this is used to calculate the exchange rate for you.
     * @param currency2 The cryptocurrency of the transaction. currency1 and currency2 can be the same if you don't want any exchange rate conversion.
     * @param additional Optionally set additional fields.
     */
    public function createTransactionSimple($amount, $currencyIn, $currencyOut, $additional = [])
    {
        $acceptableFields = ['amount', 'currency1', 'currency2', 'address', 'buyer_email', 'buyer_name',
            'item_name', 'item_number', 'invoice', 'custom', 'ipn_url'];

        $request = [
            'amount' => $amount,
            'currency1' => $currencyIn,
            'currency2' => $currencyOut,
        ];

        foreach ($acceptableFields as $field) {
            if (isset($additional[$field])) {
                $req[$field] = $additional[$field];
            }
        }

        return $this->apiCall('create_transaction', $request);
    }

    public function createTransaction($req)
    {
        // See https://www.coinpayments.net/apidoc-create-transaction for parameters
        return $this->apiCall('create_transaction', $req);
    }

    /**
     * Get transaction information via transaction ID
     *
     * @param string $txID
     * @param bool $all
     * @return void
     */
    public function getTransactionInfo($txID, $all = true)
    {
        $req = array(
            'txid' => $txID,
            'full' => (int) $all

        );

        return $this->apiCall('get_tx_info', $req);
    }

    /**
     * Creates an address for receiving payments into your CoinPayments Wallet.<br />
     * @param currency The cryptocurrency to create a receiving address for.
     * @param ipn_url Optionally set an IPN handler to receive notices about this transaction. If ipn_url is empty then it will use the default IPN URL in your account.
     */
    public function getCallbackAddress($currency, $ipnUrl = '')
    {
        $req = array(
            'currency' => $currency,
            'ipn_url' => $ipnUrl,
        );
        return $this->apiCall('get_callback_address', $req);
    }

    /**
     * Creates a withdrawal from your account to a specified address.<br />
     * @param amount The amount of the transaction (floating point to 8 decimals).
     * @param currency The cryptocurrency to withdraw.
     * @param address The address to send the coins to.
     * @param auto_confirm If auto_confirm is true, then the withdrawal will be performed without an email confirmation.
     * @param ipn_url Optionally set an IPN handler to receive notices about this transaction. If ipn_url is empty then it will use the default IPN URL in your account.
     */
    public function createWithdrawal($amount, $currency, $address, $autoConfirm = false, $ipnUrl = '')
    {
        $req = array(
            'amount' => $amount,
            'currency' => $currency,
            'address' => $address,
            'auto_confirm' => $autoConfirm ? 1:0,
            'ipn_url' => $ipnUrl,
        );
        return $this->apiCall('create_withdrawal', $req);
    }

    /**
     * Creates a transfer from your account to a specified merchant.<br />
     * @param amount The amount of the transaction (floating point to 8 decimals).
     * @param currency The cryptocurrency to withdraw.
     * @param merchant The merchant ID to send the coins to.
     * @param auto_confirm If auto_confirm is true, then the transfer will be performed without an email confirmation.
     */
    public function createTransfer($amount, $currency, $merchant, $autoConfirm = false)
    {
        $req = array(
            'amount' => $amount,
            'currency' => $currency,
            'merchant' => $merchant,
            'auto_confirm' => $autoConfirm ? 1:0,
        );
        return $this->apiCall('create_transfer', $req);
    }

    /**
     * Creates a transfer from your account to a specified $PayByName tag.<br />
     * @param amount The amount of the transaction (floating point to 8 decimals).
     * @param currency The cryptocurrency to withdraw.
     * @param pbntag The $PayByName tag to send funds to.
     * @param auto_confirm If auto_confirm is true, then the transfer will be performed without an email confirmation.
     */
    public function sendToPayByName($amount, $currency, $pbntag, $autoConfirm = false)
    {
        $req = array(
            'amount' => $amount,
            'currency' => $currency,
            'pbntag' => $pbntag,
            'auto_confirm' => $autoConfirm ? 1:0,
        );
        return $this->apiCall('create_transfer', $req);
    }

    /**
     * Validate the IPN request and payment.
     *
     * @param  array  $postData
     * @param  array  $serverData
     * @return mixed
     */
    public function validate(array $postData, array $serverData)
    {
        if (!isset($postData['ipn_mode'], $postData['merchant'], $postData['status'], $postData['status_text'])) {
            throw new \Exception("Insufficient POST data provided.");
        }
        if ($postData['ipn_mode'] == 'httpauth') {
            if ($serverData['PHP_AUTH_USER'] !== $this->merchantID) {
                throw new \Exception("Invalid merchant ID provided.");
            }

            if ($serverData['PHP_AUTH_PW'] !== $this->ipnSecret) {
                throw new \Exception("Invalid IPN secret provided.");
            }
        } elseif ($postData['ipn_mode'] == 'hmac') {
            $hmac = hash_hmac("sha512", file_get_contents('php://input'), $this->ipnSecret);

            if ($hmac !== $serverData['HTTP_HMAC']) {
                throw new \Exception("Invalid HMAC provided.");
            }
            if ($postData['merchant'] !== $this->merchantID) {
                throw new \Exception("Invalid merchant ID provided.");
            }
        } else {
            throw new \Exception("Invalid IPN mode provided.");
        }
        $order_status = $postData['status'];
        $order_status_text = $postData['status_text'];
        if ($order_status < 0) {
            throw new \Exception("{$order_status}: {$order_status_text}");
        } elseif ($order_status >= 0 && $order_status < 100 && $order_status != 2) {
            return false;
        }
        return true; // If $order_status is >100 or is 2, return true
    }

    private function isSetup()
    {
        return (!empty($this->privateKey) && !empty($this->publicKey));
    }

    private function apiCall($cmd, $req = array())
    {
        if (!$this->isSetup()) {
            return array('error' => 'You have not called the Setup function with your private and public keys!');
        }

        // Set the API command and required fields
        $req['version'] = 1;
        $req['cmd'] = $cmd;
        $req['key'] = $this->publicKey;
        $req['format'] = 'json'; //supported values are json and xml

        // Generate the query string
        $postData = http_build_query($req, '', '&');

        // Calculate the HMAC signature on the POST data
        $hmac = hash_hmac('sha512', $postData, $this->privateKey);

        // Create cURL handle and initialize (if needed)
        if ($this->ch === null) {
            $this->ch = curl_init('https://www.coinpayments.net/api.php');
            curl_setopt($this->ch, CURLOPT_FAILONERROR, true);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('HMAC: '.$hmac));
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData);

        $data = curl_exec($this->ch);
        if ($data !== false) {
            if (PHP_INT_SIZE < 8 && version_compare(PHP_VERSION, '5.4.0') >= 0) {
                // We are on 32-bit PHP, so use the bigint as string option. If you are using any API calls with Satoshis it is highly NOT recommended to use 32-bit PHP
                $dec = json_decode($data, true, 512, JSON_BIGINT_AS_STRING);
            } else {
                $dec = json_decode($data, true);
            }
            if ($dec !== null && count($dec)) {
                return $dec;
            } else {
                // If you are using PHP 5.5.0 or higher you can use json_last_error_msg() for a better error message
                return array('error' => 'Unable to parse JSON result ('.json_last_error().')');
            }
        } else {
            return array('error' => 'cURL error: '.curl_error($this->ch));
        }
    }
}

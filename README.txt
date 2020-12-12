# Pixels for Drupal

Now a static site, just a single Nginx container.

Commands use to make the site static:

```bash
wget --adjust-extension --mirror --page-requisites --convert-links -e robots=off https://pixelsfordrupal.com/
```

To capture the popups, we need to be a little creative:

```bash
for i in {1..400}; do echo $i; curl -s "https://pixelsfordrupal.com/ga.php?AID=${i}&t=1607033203" -o popups/${i}.html ; done
```

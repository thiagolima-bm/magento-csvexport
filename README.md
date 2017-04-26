# Magento CSV Export Extension

Extension that enables the generation of large CSV files.

With this extension you can generate and download huge csv files. Every time you need a updated csv you can go to the admin panel and trigger the cron. 
The cron is only executed when you say so, I mean, you say to the extension: I want a new updated csv! This behaviour avoids wasting of processing. (Let´s save the environment!)
## Features

* Export Customers
* Export Orders
* Export Products

## Installation
* Modman
```bash
modgit add csvexport git@github.com:thiagolima-bm/magento-csvexport.git 
```
* Downloading

## Configuration

This extension works is pretty much plug and play. You may need enabled it :)
To do so, go to System > Configuration > Acaldeira > Csv Exporter
In the tab CSV Exporter: Enabled = Yes

Customers Header
```bash
name;email;gender:dob
```
Customers Template
```bash
{{var customer.name}};{{var customer.email}};{{var customer.gender}};{{var customer.dob}}
```

Orders Header
```bash
increment_id;created_at;grand_total;customer_email
```
Orders Template
```bash
{{var order.increment_id}};{{var order.created_at}};{{var order.grand_total}};{{var order.customer_email}}

```

Catalog Header
```bash
name;sku;price
```
Catalog Template
```bash
{{var product.name}};{{var product.sku}};{{var product.price}}
```

## Documentation

The code is self explanatory but if you have any questions, do not hesitate in contacting me.


## Contributing

Want to contribute? That's great! [Here's you can get started](https://guides.github.com/activities/contributing-to-open-source/#contributing).

If your contribution doesn't fit with an existing issue, go ahead and [create an issue](https://github.com/thiagolima-bm/magento-csvexport/issues/new) before submitting a [Pull Request](https://help.github.com/articles/about-pull-requests/).


## Customizations and new features
If you need some customizations or new features and do not know how to do it, please contact me :) 


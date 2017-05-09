# Magento CSV(huge) Export Extension

Extension that enables the generation of large CSV files.

With this extension you can generate and download huge csv files. Every time you need a updated csv you can go to the admin panel and trigger the cron. 
The cron is only executed when you say so, I mean, you say to the extension: I want a new updated csv! This behaviour avoids wasting of processing. (Let´s save the environment!)

![Main Config](http://image.prntscr.com/image/8e2294c38a5d497a9ef8425c8bcc0289.png "Main Configuration")
![Customer Config](http://image.prntscr.com/image/3d4e1924386e4bae87d32d82f27d54d3.png "Main Configuration")

It also enables you create report from views or tables

![Custom Report](http://image.prntscr.com/image/b1a79aab40194f89940bb2b3ea3df995.png "Custom Report Configuration")

## Features

* Export Customers
* Export Orders
* Export Products
* Export Content From Specific Views
* Export Content From Specific Tables

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

In order to enable the custom reports, you have to enable it at: System > Configuration > Acaldeira > Csv Exporter

![Custom Report](http://image.prntscr.com/image/7f135101adc74037822241d22456bd44.png "Custom Report Configuration")

Then you can create as many report as you want :)

![Custom Report](http://image.prntscr.com/image/dc4bdd754d30494b9d2591bb19bc823c.png "Custom Report Configuration")



### You can use as many attributes as you want, these are just a few simple example. 

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

If you do not know how to create a mysql view, is pretty simple:

Supposing that you have this query to extract the coupon usage report:

```bash
select u.coupon_id, c.code, count(*) as total from salesrule_coupon_usage u, salesrule_coupon c where u.coupon_id=c.coupon_id  group by u.coupon_id;
```
And the query for creating the view becomes:

```bash
create view view_coupon_usage AS select u.coupon_id, c.code, count(*) as total from salesrule_coupon_usage u, salesrule_coupon c where u.coupon_id=c.coupon_id  group by u.coupon_id;
```

This is it. Pretty simple. If you open your database, you will probably see a new table (view) "view_coupon_usage"

![Custom Mysql View Report](http://image.prntscr.com/image/a1f73afd84974fbfb34cbd369f4e955f.png "Custom Mysql View Report")

Now you can go to Report > CSV Reports > Add Report and create a new custom report. (use the same view name "view_coupon_usage" in this case)


## Contributing

Want to contribute? That's great! [Here's you can get started](https://guides.github.com/activities/contributing-to-open-source/#contributing).

If your contribution doesn't fit with an existing issue, go ahead and [create an issue](https://github.com/thiagolima-bm/magento-csvexport/issues/new) before submitting a [Pull Request](https://help.github.com/articles/about-pull-requests/).


## Customizations and new features
If you need some customizations or new features and do not know how to do it, please contact me :) 


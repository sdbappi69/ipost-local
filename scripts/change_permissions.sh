#!/bin/bash

#Application Permission
chown -Rf root:ipost /var/web/html_new/ipost
chmod -Rf 770 /var/web/html_new/ipost
setfacl -d -R -m g::rwx /var/web/html_new/ipost
setfacl -R -m u:sslweb:rx /var/web/html_new/ipost
setfacl -d -R -m u:sslweb:rx /var/web/html_new/ipost
chmod -Rf g+s /var/web/html_new/ipost
chown -Rf sslweb:ipostweb /var/web/html_new/ipost/storage /var/web/html_new/ipost/bootstrap/cache /var/web/html_new/ipost/public/images /var/web/html_new/ipost/public/uploads
chmod -Rf 770 /var/web/html_new/ipost/storage /var/web/html_new/ipost/bootstrap/cache /var/web/html_new/ipost/public/images /var/web/html_new/ipost/public/uploads
setfacl -d -R -m g::rwx /var/web/html_new/ipost/storage /var/web/html_new/ipost/bootstrap/cache /var/web/html_new/ipost/public/images /var/web/html_new/ipost/public/uploads
setfacl -R -m u:sslweb:rwx /var/web/html_new/ipost/storage /var/web/html_new/ipost/bootstrap/cache /var/web/html_new/ipost/public/images /var/web/html_new/ipost/public/uploads
setfacl -d -R -m u:sslweb:rwx /var/web/html_new/ipost/storage /var/web/html_new/ipost/bootstrap/cache /var/web/html_new/ipost/public/images /var/web/html_new/ipost/public/uploads
chmod -Rf g+s /var/web/html_new/ipost/storage /var/web/html_new/ipost/bootstrap/cache /var/web/html_new/ipost/public/images /var/web/html_new/ipost/public/uploads
#Selinux Rules
chcon -R -t httpd_sys_content_rw_t /var/web/html_new/ipost/storage /var/web/html_new/ipost/bootstrap/cache /var/web/html_new/ipost/public/images /var/web/html_new/ipost/public/uploads

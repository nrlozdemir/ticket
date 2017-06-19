Ticket Application
========================
Welcome to Ticket Application

For details on how to download and get started with Ticket Application, see the
**Installation** chapter of the documentation.

What's inside?
--------------
Ticket Application uses *zend framework 1.12.17dev* framework, Bootstrap v3.3.7 and jQuery-3.2.1. 

You can find a database file for importing in the root directory.
Database connection properties can be setted in application/configs/application.ini file.

***Installation***
========================
```yaml
set include_path to extracted ZendFramework-1.12.17-minimal.zip with `library` directory
For further information, please find below the link to download.
https://framework.zend.com/downloads/archives
```
```yaml
git clone https://github.com/nrlozdemir/ticket.git
cd ticket
```
Here is your ticket directory.
```sql
# Create a database that named as "zf_app";
# Also check database properties `application/configs/application.ini`
mysql -h hostname -u username -p zf_app < zf_app.sql
```

***Run your browser with root directory adding "/public" folder or a virtual host like http://ticket/public ***
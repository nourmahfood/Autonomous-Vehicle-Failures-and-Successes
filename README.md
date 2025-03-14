
MyStore - Autonomous Vehicle Data Management

MyStore is a web-based application designed to manage and analyze data related to autonomous vehicles. It includes functionalities for tracking failures, successes, vehicle sensors, test scenarios, maintenance records, and more.

 Features
- Manage and categorize autonomous vehicle failures and successes.
- Store and retrieve sensor data.
- Track software updates and versions.
- Maintain records of vehicle tests and interventions.
- Secure database connection and data management.

 Installation
1. Clone the repository or download the files.
2. Ensure you have a web server with PHP and MySQL installed (e.g., XAMPP, WAMP, LAMP).
3. Place the project files in your web server's root directory (e.g., `htdocs` for XAMPP).
4. Create the database:
   - Open phpMyAdmin at [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
   - Create a new database called `mystore`.

5. Import the SQL file:
   - Download the SQL file from the `database/` folder in the repository.
   - In phpMyAdmin, create the `mystore` database, then click the "Import" tab.
   - Choose the `mystore.sql` file from the `database/` folder.
     
6. Ensure that `db.php` contains a compatible database configuration with `mystore.sql`.
7. Start your web server and access the application via `http://localhost/mystore/`.

 Usage
- Open the homepage (`index.html`) to navigate through the system.
- Use the various modules to enter and manage autonomous vehicle data.
- Ensure database connectivity for proper functionality.

 Requirements
- PHP 7.0+
- MySQL Database
- Apache Web Server
- Web Browser (Chrome, Firefox, Edge, etc.)



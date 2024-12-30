# Project Management System Installation Guide

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with `mod_rewrite` enabled
- Composer (PHP package manager)



## Installation Steps

1. Clone the repository:

   ```bash
   git clone https://github.com/bmackenty/projects.git
   cd <project_directory>
   ```

2. Create a MySQL database for the application (I use mariaDB and it works identically).

3. Create the database schema by importing the SQL file:
   
   `mysql -u <username> -p <database_name> < database/schema.sql`
  

4. Create a database configuration file at `config/database.php`:
```php
   <?php
   return [
       'host' => 'localhost',
       'database' => '<database_name>',
       'username' => '<username>',
       'password' => '<password>',
   ];
```

5. Configure your Apache virtual host to point to the project's root directory.
  
   Ensure the `.htaccess` file is properly configured:
```apache
      RewriteEngine On
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteRule ^(.*)$ index.php [QSA,L]
      
      # Protect uploads directory
      <IfModule mod_rewrite.c>
          RewriteRule ^public/uploads/ - [F,L]
      </IfModule>
```

6. Set proper permissions
  ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
  ```

7. Create the uploads directory:
```bash
   mkdir public/uploads
   chmod 755 public/uploads
 ```

8. Default admin credentials:
   - Email: `admin@example.com`
   - Password: `admin123`



## Security Considerations

- Change the default admin password immediately after installation.
- Update the `config/database.php` file permissions:

  chmod 640 config/database.php

- Ensure your `public/uploads` directory is properly secured through `.htaccess`:
```apache
  <FilesMatch ".*">
      Order Allow,Deny
      Deny from all
  </FilesMatch>
```

---

## Optional Configuration

- Configure TinyMCE editor (if you want to use your own API key):
```javascript
    tinymce.init({
        selector: '.wysiwyg-editor',
        plugins: 'lists link image table code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
        height: 300,
        menubar: false,
        branding: false,
        promotion: false,
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
        paste_as_text: true,
        paste_enable_default_filters: true,
        paste_word_valid_elements: "b,strong,i,em,h1,h2,h3,h4,h5,h6,p,ul,ol,li",
        paste_retain_style_properties: "none"
    });
```



## Troubleshooting

### If you encounter permission issues:
- Check file ownership (should be your web server user):

  `chown -R www-data:www-data <project_directory>`

- Verify upload directory permissions:

 ` chmod -R 755 public/uploads`

- Ensure database user has proper privileges.

### If routes aren't working:
- Verify `mod_rewrite` is enabled:
```bash
  a2enmod rewrite
  service apache2 restart
```
- Check `.htaccess` file is being read.
- Confirm `RewriteBase` is set correctly if not in the root directory.

### For database connection issues:
- Verify database credentials.
- Check MySQL user privileges.
- Ensure PDO extension is enabled in PHP:

  `php -m | grep pdo`


## Additional Notes

- The application uses PHP sessions for authentication.
- File uploads are restricted to PDF and image files.
- Maximum upload size is set to 5MB by default.

Remember to configure your PHP settings (`php.ini`) for:
```
  upload_max_filesize = 5M
  post_max_size = 5M
  memory_limit = 128M
```

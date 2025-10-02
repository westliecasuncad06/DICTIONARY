# TODO for Adding Database for Admin and User with MD5 Passwords

- [x] Create users.sql with users table schema and default admin/user inserts
- [x] Create setup_users.php to execute users.sql and delete itself
- [x] Modify index.php to use MD5 for password verification
- [x] Modify register.php to use MD5 for password hashing
- [x] Modify db.php to remove users table creation
- [ ] Run setup_users.php to apply database changes
- [ ] Test login with admin and user credentials

# Gitserver Specification (ver. 0.1)
## Hardware Specification
 - Disk size 8 GB
 - One 4.2 GB Partition

## System Specification
 - Debian 9

## Network Specification
 - Static IP Address `10.0.2.20\30`
 - `ssh` listens on port 2222.
 - `apache2` listens on port 8080.
 - `gitserver-access` listens on port 57348.
 - All other ports are unused.

See also [Security Specification](#security-specification).
## Users and Groups
 - `maint` is for maintenance of the server itself and is a sudoer.
 - `git` is for managing repositories. The apache web server runs as `git`.
 - `web_maint` is for maintenace of the web interface.
   - group `web_maint` has write privileges on all web interface documents.
   - users `maint` and `web_maint` are the only members of group `web_maint`.
## Scheduled and Boot Tasks

 - Once a day a script (`/usr/bin/check_crontab_modified`) will run that will detect changes to `/etc/crontab`, and sends an email to root if it has been modified.
 - Once a week at 4 AM, a script (`/etc/init.d/gitserver_update`) will run to update all installed packages and logs the results in `/var/log/update_script.log`. The script will also run every time the machine reboots (`/etc/rc4.d/S01gitserver_update`). The script also checks that the previous script to check crontab is still properly installed, and if not, sends an email to root.
- Every day at 4:30 AM, a script (`/usr/bin/gitserver_backup`) will run to backup all the git repositories on the server, and delete backups that are more than a week old.
- The services `apache2`, `sendmail`, `cron`, `mariadb`, `ssh`, and `gitserver-access` are all started at boot time. See `/etc/rc*.d`. This is in addition to the necessary system services `getty`, `dbus`, `systemd`, and `rsyslog`.

## Security Specification
- Authorized IP addresses can access only the following ports:
  - 2222 (SSH)
  - 8080 (Web interface)
  - 57348 (gitserver-access)
- Unrecognized IP address can access only the following ports:
  - 57348 (gitserver-access)
- The firewall rules are read from `/etc/iptables-rules`. The firewall rules are loaded on boot via the use of `/etc/network/if-pre-up.d/iptables`.

### The `gitserver-access` Service
The `gitserver-access` service can be connected to with a TCP connection and accept input. If it recieves an SSH public key that is present in `/home/maint/.ssh/authorized_keys`, then the IP address that initiated the connection will henceforth be allowed to use other services and the change in firewall rules will be noted in `/var/log/gitserver_access.log`. If the input is anything other than an authorized maintenance SSH-RSA public key, the connection will be closed with no feedback, and the incident will be logged in `/var/log/gitserver_access.log`.

### DDOS and Port Scan Protections
- `gitserver-access` being the only service open to external queries, strict limits are placed on it to avoid it being exploited by DDOS attacks or port scans. An average wait time of 10 seconds between subsequent connections will be enforced by an iptables connlimit rule.
- Basic, less restrictive DDOS and port scan protections are provided on all other connections.

### Database Security
 - The SQL database is not accessible outside the server.
 - MariaDB User `gitserver_access` has only the grants `SELECT`, `INSERT` on the database/table `gitserver_access.connected_addresses` and no others.
 - MariaDB User `git` has only the grants `SELECT`, `INSERT`, `UPDATE`, `SHOW VIEW`, `TRIGGER` on all tables of database `gitserver` and no others.
 - All MariaDB users are password-protected.
 - Passwords needed by source code are read from environment variables set up by configuration files.

### Other Security Specifications
 - Passwords for web interface users are salted and hashed using the whirlpool algorithm.

## Web Interface Specification
The web interface allows users to view and open repositories on the server.

### Apache Web Server Configuration
 - (VM testing only) The web interface can be accessed for testing at `localhost:8080`.
 - The document root is `/var/www/html/`.
 - The default document is `/index.php`.
 - apache2 runs as user `git`.

### Landing Page
The default page shows a list of repositories on the server, information about the latest commit, and a URL for the remote repo that will be usable once a user is completely configured.

### Connect New User Page
A first-time user can make an account by using the **New User** link in the navbar.
   - Usernames must be between 4 and 32 characters. The acceptable characters for usernames are [`A-Za-z0-9_.@`].
   - Email is optional.
   - Passwords must be at least 3 characters and 'Password' must match 'Confirm Password'.
   - If input is given to the SSH key text area, it must be a valid SSH public key.
   - The user is able to 'upload' an SSH key by using the 'Choose File' button. This populates the SSH key text area.
   - On error, the user is shown a helpful message and stays on the page.
   - On successful user creation, the user is shown a message indicating such and is redirected to the login page.
   - The page also shows information on how to set up SSH on their computer to work with gitserver.
 
 ### Login Page
  - The user must be able to log in with the username and password they selected.
  - The user must not be able to log in with any other combination.
  - On error, the user is shown a helpful message and stays on the page.
  - On successful login, the user is shown a message indicating such and is redirected to the repository list.

### Open New Repository Page
 - This page must only be visible to logged in users.
 - Repo names must be between 3 and 100 characters, accept only [`A-Za-z0-9_`], and cannot duplicate names of repos that already exist.
 - Descriptions must not be empty, and accept any character except `<` and `>`.
   - This includes `'`.
 - On error, the user is shown a helpful message and stays on the page.
 - On success, the user is shown a message indicating such and is redirected to the repository list, where the new repository is shown.

### My Account Page
 - The user is able change their password. All the same password rules apply.
 - The user is able to add an additional SSH key. All the same SSH key rules apply.
 - The user is shown information on how to set up SSH on their computer to work with gitserver.
 - On error of either password change or add SSH key, the user is shown a helpful message and stays on the page.
 - On success of either password change or add SSH key, the user is shown a message indicating such and stays on the page.

## Usage

Once a user has completed the required SSH setup, they can clone, fetch, push, pull, etc as normal for a remote repository, using the urls provided on the Server Repository list.

This repo is not provided for cloning and production use. The files in this repo are the files that I modified from default configurations to produce the end product. This page is a specification for 'gitserver', which I submitted as the 42 project roger-skyline-1.

# Gitserver Specification (ver. 0.1)
## Hardware Specification &#x2713;
 - Disk size 8 GB &#x2713;
 - One 4.2 GB Partition &#x2713;

## System Specification &#x2713;
 - Debian 9 &#x2713;

## Network Specification
 - Static IP Address `10.0.2.20\30`  &#x2713;
 - (VM testing only) 2nd Static IP Address `192.168.99.110/30` &#x2713;
 - `ssh` listens on port 2222. &#x2713;
 - `apache2` listens on port 8080 (HTTP). &#x2713;
 - `apache2` listens on port 44343 (HTTPS). &#x2713;
 - `gitserver-access` listens on port 57348. &#x2713;
 - All other ports are unused. &#x2713;

See also [Security Specification](#security-specification).
## Users and Groups &#x2713;
 - `maint` is for maintenance of the server itself and is a sudoer. &#x2713;
 - `git` is for managing repositories. The apache web server runs as `git`. &#x2713;
 - `web_maint` is for maintenace of the web interface.  &#x2713;
   - group `web_admin` has write privileges on all web interface documents.  &#x2713;
   - users `maint` and `web_maint` are the only normal members of group `web_admin`.  &#x2713;
## Scheduled and Boot Tasks &#x2713;

 - Once a day a script (`/usr/bin/check_crontab_modified`) will run that will detect changes to `/etc/crontab`, and sends an email to root if it has been modified. &#x2713;
 - Once a week at 4 AM, a script (`/etc/init.d/gitserver_update`) will run to update all installed packages and logs the results in `/var/log/update_script.log`. The script will also run every time the machine reboots (`/etc/rc4.d/S01gitserver_update`). The script also checks that the previous script to check crontab is still properly installed, and if not, sends an email to root. &#x2713;
- Every day at 4:30 AM, a script (`/usr/bin/gitserver_backup`) will run to backup all the git repositories on the server, and delete backups that are more than a week old. &#x2713;
- The services `apache2`, `sendmail`, `cron`, `mariadb`, `ssh`, and `gitserver-access` are all started at boot time. See `/etc/rc*.d`. This is in addition to the necessary system services `getty`, `dbus`, `systemd`, and `rsyslog`. &#x2713;

## Security Specification  &#x2713;
- Authorized IP addresses can access only the following ports: &#x2713;
  - 2222 (SSH)
  - 8080 (Web interface HTTP)
  - 44343 (Web interface HTTPS)
  - 57348 (gitserver-access)
- Unrecognized IP address can access only the following ports: &#x2713;
  - 57348 (gitserver-access)
- The firewall rules are read from `/etc/iptables-rules`. The firewall rules are loaded on boot via the use of `/etc/network/if-pre-up.d/iptables`. &#x2713;

### The `gitserver-access` Service &#x2713;
The `gitserver-access` service can be connected to with a TCP connection and accept input. If it recieves an SSH public key that is present in `/home/maint/.ssh/authorized_keys`, then the IP address that initiated the connection will henceforth be allowed to use other services and the change in firewall rules will be noted in `/var/log/gitserver_access.log`. If the input is anything other than an authorized maintenance SSH-RSA public key, the connection will be closed with no feedback, and the incident will be logged in `/var/log/gitserver_access.log`. &#x2713;

### DDOS and Port Scan Protections &#x2713;
- `gitserver-access` being the only service open to external queries, strict limits are placed on it to avoid it being exploited by DDOS attacks or port scans. An average wait time of 10 seconds between subsequent connections will be enforced by an iptables connlimit rule. &#x2713;
- Basic, less restrictive DDOS and port scan protections are provided on all other connections. &#x2713;

### Database Security  &#x2713;
 - The SQL database is not accessible outside the server. &#x2713;
 - MariaDB User `gitserver_access` has only the grants `SELECT`, `INSERT` on the database/table `gitserver_access.connected_addresses` and no others. &#x2713;
 - MariaDB User `git` has only the grants `SELECT`, `INSERT`, `UPDATE`, `SHOW VIEW`, `TRIGGER` on all tables of database `gitserver` and no others. &#x2713;
 - All MariaDB users are password-protected. &#x2713;
 - Passwords needed by source code are read from environment variables and/or set up by root-only configuration files.  &#x2713;

### Other Security Specifications &#x2713;
 - Passwords for web interface users are salted and hashed using the whirlpool algorithm. &#x2713;

## Web Interface Specification &#x1F538;
The web interface allows users to view and open repositories on the server.

### Apache Web Server Configuration &#x2713;
 - (VM testing only) The web interface can be accessed for testing at `localhost:8080`. &#x2713;
 - The document root is `/var/www/html/`. &#x2713;
 - The default document is `/index.php`. &#x2713;
 - apache2 runs as user `git`. &#x2713;
 - .htaccess files prohibit users from accessing anything outside the user interface, required scripts, and CSS.  &#x2713;
 - HTTPS uses self-signed SSL certificate.  &#x2713;

### Landing Page &#x2713;
The default page shows a list of repositories on the server, information about the latest commit, and a URL for the remote repo that will be usable once a user is completely configured. &#x2713;

### Connect New User Page &#x2713;
A first-time user can make an account by using the **New User** link in the navbar.
   - Usernames must be between 4 and 32 characters. The acceptable characters for usernames are [`A-Za-z0-9_.@`]. &#x2713;
   - Email is optional. &#x2713;
   - Passwords must be at least 3 characters and 'Password' must match 'Confirm Password'. &#x2713;
   - If input is given to the SSH key text area, it must be a valid SSH public key. &#x2713;
   - The user is able to 'upload' an SSH key by using the 'Choose File' button. This populates the SSH key text area. &#x2713;
   - On error, the user is shown a helpful message and stays on the page. &#x2713;
   - On successful user creation, the user is shown a message indicating such and is redirected to the login page. &#x2713;
   - The page also shows information on how to set up SSH on their computer to work with gitserver. &#x2713;
 
 ### Login Page &#x2713;
  - The user must be able to log in with the username and password they selected.  &#x2713;
  - The user must not be able to log in with any other combination. &#x2713;
  - On error, the user is shown a helpful message and stays on the page. &#x2713;
  - On successful login, the user is shown a message indicating such and is redirected to the repository list. &#x2713;

### Open New Repository Page &#x1F538;
 - This page must only be visible to logged in users. &#x2713;
 - Repo names must be between 3 and 100 characters, accept only [`A-Za-z0-9_`], and cannot duplicate names of repos that already exist. &#x2713;
 - Descriptions must not be empty, and accept any character except `<` and `>`.
   - This includes `'`. &#x274C; >> *Currently disallows apostrophe. Implementation of this specification is postponed to version 0.2*
 - On error, the user is shown a helpful message and stays on the page.  &#x2713;
 - On success, the user is shown a message indicating such and is redirected to the repository list, where the new repository is shown. &#x2713;

### My Account Page &#x2713;
 - The user is able change their password. All the same password rules apply. &#x2713;
 - The user is able to add an additional SSH key. All the same SSH key rules apply. &#x2713;
 - The user is shown information on how to set up SSH on their computer to work with gitserver. &#x2713;
 - On error of either password change or add SSH key, the user is shown a helpful message and stays on the page. &#x2713;
 - On success of either password change or add SSH key, the user is shown a message indicating such and stays on the page. &#x2713;

## Usage &#x2713;

Once a user has completed the required SSH setup, they can clone, fetch, push, pull, etc as normal for a remote repository, using the urls provided on the Server Repository list. &#x2713;

# Notes for correction

 - `fdisk -l` shows partitions. This shows `/dev/sda1` as 3.9G, which should be the 4.2G partition. `resize2fs -z /tmp/undo-rfs /dev/sda1 4404019K` makes it 4.2G. `fdisk` will still show 3.9G. But if you boot from disk and go through the install process up until the disk detection, the partition will be detected as 4.2G.


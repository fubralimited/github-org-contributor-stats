# github-org-contributor-stats
Easily view all your contributor commit statistics to your organisation's projects in one place.

# Dependencies

This project requires composer to install dependencies such as [knplabs/github-api](https://github.com/KnpLabs/php-github-api). You can find some instructions to install composer [here](https://getcomposer.org/doc/00-intro.md), or you could just do...

```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

# Setup 

Clone the repository
```
git clone git@github.com:fubralimited/github-org-contributor-stats.git
```

Run composer to install dependencies.
```
cd github-org-contributor-stats
composer install
```

Add your github API keys to the config file. You can set up a personal access token [here](https://github.com/settings/tokens).

```
cp config.php.example config.php
vim config.php
```

Install ansible role requirements
```
sudo ansible-galaxy install -r requirements.yml
```


# Running the import script

Import your repository commit data into the local SQLite database
```
php -f src/import.php
```

# Vagrant & Ansible

This project includes a VagrantFile and Ansible configuration to set up a local LEMP (Linux, NGINX, MySQL, PHP) test environment from which you can run the scripts. To use this, you will need [VirtualBox](https://www.virtualbox.org/wiki/Downloads), [Vagrant](https://www.vagrantup.com/downloads.html) and [Ansible](http://docs.ansible.com/ansible/intro_installation.html) installed, then do:

```
vagrant up
```

You should then be able to access the site via your browser on http://192.168.33.34

# Author Information

* Paul Maunders (Fubra Limited)
* Jeff Geerling - [Ansible LEMP code](https://github.com/geerlingguy/ansible-vagrant-examples/tree/master/lemp)

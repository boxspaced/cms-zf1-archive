# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

  config.vm.box = "bento/centos-6.8"

  config.vm.network "private_network", ip: "192.168.56.101"

  config.vm.synced_folder "./", "/var/cms", mount_options: ['dmode=777', 'fmode=776']
  config.vm.synced_folder ".", "/vagrant", disabled: true

  config.vm.provider "virtualbox" do |vb|
    vb.memory = "2048"
  end

  config.vm.provision "shell", privileged: true, inline: <<-SHELL

    rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm
    rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm

    yum -y update
    yum -y install vim httpd mysql mysql-server php55w-mysql php55w-mbstring php55w-common php55w-cli php55w php55w-xml php55w-gd php55w-pdo php55w-opcache java-1.8.0-openjdk-headless firefox Xvfb

    sed -i -e 's#/var/www/html#/var/cms/public#g' /etc/httpd/conf/httpd.conf
    bash -c 'echo "SetEnv APPLICATION_ENV development" >> /etc/httpd/conf/httpd.conf'
    bash -c 'echo "APPLICATION_ENV=\"development\"" >> /etc/environment'
    bash -c 'echo "<Directory "/var/cms/public">" >> /etc/httpd/conf/httpd.conf'
    bash -c 'echo "    AllowOverride All" >> /etc/httpd/conf/httpd.conf'
    bash -c 'echo "</Directory>" >> /etc/httpd/conf/httpd.conf'
    bash -c 'echo "127.0.0.1 www.cms.dev cms.dev" >> /etc/hosts'

    wget -O /usr/local/bin/selenium-server http://selenium-release.storage.googleapis.com/2.53/selenium-server-standalone-2.53.1.jar
    chmod +x /usr/local/bin/selenium-server

    wget -O /usr/local/bin/mailhog https://github.com/mailhog/MailHog/releases/download/v0.2.0/MailHog_linux_amd64
    chmod +x /usr/local/bin/mailhog

  SHELL

  config.vm.provision "shell", run: "always", privileged: true, inline: <<-SHELL

    service httpd start
    service mysqld start

    rm -rf /tmp/.X*-lock
    DISPLAY=:1 xvfb-run --server-args="-screen 0 1280x1024x8" java -jar /usr/local/bin/selenium-server &

    nohup /usr/local/bin/mailhog > /dev/null 2>&1 &

  SHELL

  config.vm.provision "shell", privileged: true, inline: <<-SHELL

    cd /var/cms
    bin/phing build -Dbuild.env=development
    chown vagrant /tmp/log

  SHELL

end

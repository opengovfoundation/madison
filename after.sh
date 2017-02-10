#!/bin/sh

# Fix MariaDB

sudo ln -sf /etc/apparmor.d/usr.sbin.mysqld /etc/apparmor.d/disable/usr.sbin.mysqld

sudo systemctl restart apparmor
sudo systemctl restart mysql


# Setup stuff for Dusk

sudo apt-get update

sudo apt-get -y install \
     chromium-browser \
     xvfb \
     gtk2-engines-pixbuf \
     xfonts-cyrillic \
     xfonts-100dpi \
     xfonts-75dpi \
     xfonts-base \
     xfonts-scalable \
     imagemagick \
     x11-apps \

sudo tee /etc/systemd/system/xvfb@.service > /dev/null <<EOF
[Unit]
Description=virtual frame buffer X server for display %I
After=network.target

[Service]
ExecStart=/usr/bin/Xvfb %I -screen 0 1280x1024x24

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl enable xvfb@:0.service
sudo systemctl start xvfb@:0.service

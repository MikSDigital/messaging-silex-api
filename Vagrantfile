Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/trusty64"

  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "private_network", ip: "192.168.33.11"


  config.vm.provision "file",
    source: "provision/vhosts/dev.silex-api.com.conf",
    destination: "/home/vagrant/dev.silex-api.com.conf"

  config.vm.provision "shell" do |s|
    s.path = "provision/setup.sh"
    s.args = ["chatter", "root", "root", "dev.silex-api.com"]
  end
end

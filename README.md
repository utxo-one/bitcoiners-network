# Bitcoiners.Network

Bitcoiners.Network is the ultimate way to manage your Bitcoin Twitter experience. Discover new bitcoiners to follow, unfollow shitcoiners and nocoiners and discover the best content from fellow bitcoiners.

To get started, visit https://bitcoiners.network

## Local Installation

If you're interested in running a local instance of this app and building your own database of users, read the instructions below.

### Prerequisites

 - 150GB Disk Space
 - 8GB RAM
 - Linux
 - Docker
 - Twitter Developer Credentials
 - BTCPayServer + Lightning Node

## Installation

#### Clone Repository

```sh
git clone https://github.com/utxo-one/bitcoiners-network
```
#### Give Write Permissions

```sh
sudo chmod 777 -R bitcoiners-network/
```
#### Enter Directory

```sh
cd bitcoiners-network
```

#### Create .env file

```sh
sudo cp .env.example .env
```

#### Install Composer Packages
```sh
sudo composer update
```

#### Docker Up (Sail)
```sh
sudo vendor/bin/sail up -d
```

#### Run Database Migration and Seeder
```sh
sudo vendor/bin/sail artisan migrate:refresh --seed
```

App should be accessible at http://localhost


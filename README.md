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

#### Docker Up (Sail)
```sh
sudo vendor/bin/sail up -d
```

#### Run Database Migration and Seeder
```sh
sudo vendor/bin/sail artisan migrate:refresh --seed
```

App should be accessible at http://localhost


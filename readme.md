# php-slim-blog
php slim3으로 제작된 블로그 소스입니다.

## Usage
### basic setting
```sh
cp .env.example .env
cp .about.md.example .about.md
chmod 777 view/cache public/data
```
1. env 환경변수를 세팅하세요.
2. about.md 파일에 자기소개를 작성하세요.

### docker
```sh
docker-compose up -d
docker-compose exec php-fpm composer install #composer install
docker-compose exec php-fpm php migration.php #table 생성
```
1. 도커 빌드 전, `.env` 파일이 작성되어야 합니다.
2. 로컬환경에 맞춰 `docker-compose.yml`을 수정합니다.

### js, scss cli scripts
```sh
npm i #패키지 설치
npm run build # js,scss 빌드
npm run watch:php #built in php server, webpack dev server 실행
npm run watch:no-dev #webpack default watch 실행
npm run watch #webpack dev server 실행
```
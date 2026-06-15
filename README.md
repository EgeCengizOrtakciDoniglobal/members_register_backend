# members_register_backend

Üye kayıt ve yönetim sisteminin **backend (API)** servisidir. Laravel ile yazılmıştır ve token tabanlı kimlik doğrulama (Laravel Sanctum) kullanır.

---

## İçindekiler

- [Projenin Amacı](#projenin-amacı)
- [Mimari ve Teknolojiler](#mimari-ve-teknolojiler)
- [Gereksinimler](#gereksinimler)
- [Kurulum](#kurulum)
- [.env Yapılandırması](#env-yapılandırması)
- [Veritabanı ve Migration](#veritabanı-ve-migration)
- [İlk Kullanıcıyı / Admin'i Oluşturma](#ilk-kullanıcıyı--admini-oluşturma)
- [Sunucuyu Çalıştırma](#sunucuyu-çalıştırma)
- [Giriş (Login) Nasıl Yapılır?](#giriş-login-nasıl-yapılır)
- [API Uçları (Endpoints)](#api-uçları-endpoints)
- [Roller ve Yetkilendirme](#roller-ve-yetkilendirme)
- [WordPress Entegrasyon Ucu](#wordpress-entegrasyon-ucu)
- [Veritabanı Tabloları](#veritabanı-tabloları)

---

## Projenin Amacı

Bu servis, bir **üye (member) kayıt sistemini** yönetmek için tasarlanmış bir REST API'dir. Üç temel işi vardır:

1. **Üye yönetimi** — Üyeleri kaydetmek, listelemek, güncellemek ve silmek (CRUD). Her üyenin adı, soyadı, e-postası, TCKN'si, lisans numarası ve durumu (`active` / `inactive` / `pending`) tutulur.
2. **Kullanıcı (panel kullanıcısı) ve yetki yönetimi** — Sisteme giriş yapan yöneticiler ile normal kullanıcıları yönetmek. Kullanıcıların `admin` / `user` rolü vardır. Yalnızca **admin** rolündeki kullanıcılar yeni kullanıcı ekleyebilir, düzenleyebilir, silebilir ve onlara rol atayabilir.
3. **WordPress entegrasyonu** — Harici bir WordPress sitesinin (eklenti aracılığıyla), statik bir API anahtarıyla **yalnızca aktif üyelerin** listesini güvenli şekilde çekebilmesini sağlamak.

Frontend (yönetim paneli) ayrı bir projede (`members_register_frontend`) bulunur ve bu API'yi tüketir.

---

## Mimari ve Teknolojiler

| Bileşen | Kullanılan |
|---|---|
| Framework | Laravel `^13.8` |
| PHP | `^8.3` |
| Kimlik doğrulama | Laravel Sanctum `^4.0` (Bearer token) |
| Veritabanı | MySQL |
| Konsol araçları | Laravel Tinker, Pint |

Kimlik doğrulama iki şekilde yapılır:

- **Panel/uygulama tarafı:** `auth:sanctum` middleware'i — kullanıcı `/api/login` ile token alır, sonraki isteklerde `Authorization: Bearer <token>` header'ı gönderir.
- **WordPress entegrasyonu:** `apikey` middleware'i (`VerifyApiKey`) — istek `X-API-Key` header'ı ile gelir ve `.env`'deki `INTEGRATION_API_KEY` ile karşılaştırılır.

---

## Gereksinimler

- PHP 8.3+
- Composer
- MySQL (çalışan bir sunucu ve bir veritabanı)

---

## Kurulum

```bash
# 1) Bağımlılıkları yükle
composer install

# 2) .env dosyasını oluştur (aşağıdaki bölüme göre düzenle)
cp .env.example .env     # .env.example yoksa .env'yi elle oluştur

# 3) Uygulama anahtarını üret
php artisan key:generate

# 4) Veritabanı tablolarını oluştur
php artisan migrate
```

---

## .env Yapılandırması

Proje kök dizininde bir `.env` dosyası bulunmalıdır. Aşağıda **bu projenin çalışması için gereken** değişkenler ve açıklamaları yer alır.

> ⚠️ **Güvenlik:** `.env` dosyası gizli bilgiler içerir ve **asla git'e gönderilmemelidir** (`.gitignore` içinde olmalıdır). Aşağıdaki gizli değerler (`APP_KEY`, `INTEGRATION_API_KEY` vb.) **örnektir** — kendi değerlerinizi üretin, gerçek anahtarları paylaşmayın.

### Uygulama

```env
APP_NAME=Laravel
APP_ENV=local                 # local | production
APP_KEY=                      # `php artisan key:generate` ile üretilir
APP_DEBUG=true                # production'da false yapın
APP_URL=http://localhost
```

### Veritabanı (MySQL) — **giriş için en kritik kısım**

Login'in çalışması için API'nin veritabanına bağlanabilmesi gerekir. Kullanıcılar `users` tablosunda tutulduğu için, doğru DB ayarları olmadan giriş yapılamaz.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=members_db        # önceden oluşturulmuş MySQL veritabanı adı
DB_USERNAME=root              # kendi MySQL kullanıcı adınız
DB_PASSWORD=root              # kendi MySQL şifreniz
```

> Bu değerler birer **örnektir**. Kendi MySQL kurulumunuza göre `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` alanlarını doldurun. Belirtilen veritabanının (`members_db`) MySQL'de önceden oluşturulmuş olması gerekir.

### WordPress entegrasyon anahtarı

```env
# WordPress eklentisinin X-API-Key header'ında göndereceği statik anahtar.
INTEGRATION_API_KEY=BURAYA_GUVENLI_RASTGELE_BIR_ANAHTAR
```

Güçlü bir anahtar üretmek için:

```bash
php -r "echo bin2hex(random_bytes(32)).PHP_EOL;"
```

### Diğer (Laravel varsayılanları, çoğunlukla değiştirmeye gerek yok)

```env
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
LOG_CHANNEL=stack
MAIL_MAILER=log
```

---

## Veritabanı ve Migration

Tablolar migration'lar ile oluşturulur:

```bash
php artisan migrate
```

Oluşturulan başlıca tablolar:

- `users` — panel kullanıcıları (`role` alanı ile: `admin` / `user`)
- `members` — üyeler
- `personal_access_tokens` — Sanctum token'ları
- `sessions`, `cache`, `jobs` — Laravel altyapı tabloları

Veritabanını sıfırlayıp baştan kurmak için (⚠️ tüm veriyi siler):

```bash
php artisan migrate:fresh
```

---

## İlk Kullanıcıyı / Admin'i Oluşturma

Sistemde **kayıt (register) endpoint'i yoktur**; kullanıcılar yalnızca admin tarafından eklenir. Ancak ilk admin'i ekleyecek bir admin henüz olmadığı için, ilk kullanıcıyı elle oluşturmanız gerekir.

`php artisan tinker` ile:

```php
\App\Models\User::create([
    'name'     => 'Yönetici',
    'mail'     => 'admin@example.com',
    'password' => 'gizli-sifre',   // model tarafından otomatik hash'lenir
    'status'   => 'active',
    'role'     => 'admin',
]);
```

> `password` alanı, `User` modelindeki `hashed` cast'i sayesinde otomatik olarak hash'lenir; düz metin saklanmaz.

Bu kullanıcı oluşturulduktan sonra, artık panelden giriş yapıp diğer kullanıcıları API üzerinden ekleyebilirsiniz.

---

## Sunucuyu Çalıştırma

```bash
php artisan serve
```

API varsayılan olarak `http://127.0.0.1:8000` adresinde çalışır. Tüm uçlar `/api` ön ekiyle erişilir (örn. `http://127.0.0.1:8000/api/login`).

---

## Giriş (Login) Nasıl Yapılır?

Giriş, `.env` ile değil, **veritabanındaki bir kullanıcı kaydı** (mail + şifre) ile yapılır. `.env` yalnızca API'nin bu kullanıcıyı bulabilmesi için **veritabanı bağlantısını** sağlar.

### Adımlar

1. `.env` içinde DB ayarlarının doğru olduğundan emin olun.
2. `php artisan migrate` ile tabloları oluşturun.
3. Yukarıdaki gibi bir kullanıcı oluşturun.
4. Aşağıdaki istekle giriş yapın.

### İstek

```http
POST /api/login
Content-Type: application/json

{
  "mail": "admin@example.com",
  "password": "gizli-sifre"
}
```

`curl` ile:

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"mail":"admin@example.com","password":"gizli-sifre"}'
```

### Başarılı Yanıt

```json
{
  "user": { "id": 1, "name": "Yönetici", "mail": "admin@example.com", "status": "active", "role": "admin" },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer"
}
```

Geçersiz bilgi girilirse `422` doğrulama hatası döner (`Geçersiz kimlik bilgileri.`).

### Token'ı Kullanma

Dönen `token` değerini, korumalı tüm isteklerde header olarak gönderin:

```http
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Çıkış (Logout)

```http
POST /api/logout
Authorization: Bearer <token>
```

Geçerli token iptal edilir.

---

## API Uçları (Endpoints)

Tüm uçlar `/api` ön ekiyle çağrılır.

### Herkese açık

| Metot | Yol | Açıklama |
|---|---|---|
| `POST` | `/api/login` | Giriş yapar, Bearer token döner |

### Korumalı — `Authorization: Bearer <token>` gerekir

| Metot | Yol | Açıklama |
|---|---|---|
| `POST` | `/api/logout` | Oturumu kapatır (token'ı iptal eder) |
| `GET` | `/api/members` | Tüm üyeleri listeler |
| `POST` | `/api/members` | Yeni üye ekler |
| `GET` | `/api/members/{id}` | Tek üye getirir |
| `PUT/PATCH` | `/api/members/{id}` | Üye günceller |
| `DELETE` | `/api/members/{id}` | Üye siler |

### Korumalı + **yalnızca admin** — `Bearer token` + `role=admin` gerekir

| Metot | Yol | Açıklama |
|---|---|---|
| `GET` | `/api/users` | Tüm kullanıcıları listeler |
| `POST` | `/api/users` | Yeni kullanıcı ekler |
| `GET` | `/api/users/{id}` | Tek kullanıcı getirir |
| `PUT/PATCH` | `/api/users/{id}` | Kullanıcı günceller (rol dahil) |
| `DELETE` | `/api/users/{id}` | Kullanıcı siler |

Admin olmayan bir kullanıcı bu uçlara erişmeye çalışırsa `403` (`Bu işlem için yönetici yetkisi gerekir.`) döner.

#### Örnek: Kullanıcı ekleme (admin token'ı ile)

```bash
curl -X POST http://127.0.0.1:8000/api/users \
  -H "Authorization: Bearer <ADMIN_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ahmet Yılmaz",
    "mail": "ahmet@example.com",
    "password": "en-az-8-karakter",
    "role": "user",
    "status": "active"
  }'
```

**Kullanıcı alanları:** `name` (zorunlu), `mail` (zorunlu, benzersiz), `password` (zorunlu, en az 8 karakter), `role` (opsiyonel: `admin`/`user`, varsayılan `user`), `status` (opsiyonel: `active`/`inactive`/`pending`).

### WordPress entegrasyonu — `X-API-Key` header gerekir

| Metot | Yol | Açıklama |
|---|---|---|
| `GET` | `/api/v1/integration/members` | Yalnızca **aktif** üyeleri döner |

---

## Roller ve Yetkilendirme

- Her kullanıcının bir `role` alanı vardır: `admin` veya `user` (varsayılan `user`).
- `admin` rolü, kullanıcı yönetimi uçlarına (`/api/users`) erişebilen tek roldür.
- Yetki kontrolü `EnsureUserIsAdmin` middleware'i (`admin` takma adı) ile yapılır.
- Üye yönetimi (`/api/members`) ise giriş yapmış **her** kullanıcıya açıktır (admin şartı yoktur).

---

## WordPress Entegrasyon Ucu

WordPress eklentisi, üye listesini çekmek için şu isteği yapar:

```bash
curl http://127.0.0.1:8000/api/v1/integration/members \
  -H "X-API-Key: <INTEGRATION_API_KEY>"
```

- Anahtar `.env`'deki `INTEGRATION_API_KEY` ile **zamanlama-güvenli** (`hash_equals`) karşılaştırılır.
- Anahtar eksik/yanlışsa `401` döner.
- Yanıt, yalnızca `status = active` olan üyeleri `{ "data": [ ... ] }` biçiminde döner. Bu uç **salt-okunurdur**, veri değiştirmez.

---

## Veritabanı Tabloları

### `users`

| Alan | Tip | Not |
|---|---|---|
| `id` | bigint | PK |
| `name` | string | |
| `mail` | string | benzersiz |
| `password` | string | hash'lenmiş |
| `status` | enum | `active` / `inactive` / `pending` (varsayılan `active`) |
| `role` | enum | `admin` / `user` (varsayılan `user`) |
| `create_date` | datetime | kayıt zamanı (`created_at`/`updated_at` yok) |

### `members`

| Alan | Tip | Not |
|---|---|---|
| `id` | bigint | PK |
| `name` | string | |
| `lastname` | string | |
| `mail` | string | benzersiz |
| `tckn` | string(11) | benzersiz |
| `lisanceno` | string | lisans numarası |
| `status` | enum | `active` / `inactive` / `pending` (varsayılan `active`) |
| `create_date` | datetime | kayıt zamanı |

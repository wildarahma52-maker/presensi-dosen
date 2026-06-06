google_sheet.credential_path bukan didapat dari Google, tetapi path file JSON Service Account yang kamu download dari Google Cloud.

1. Buat Service Account

Buka:

https://console.cloud.google.com

Langkah:

1. Buat Project baru
2. Aktifkan Google Sheets API
3. Buka IAM & Admin → Service Accounts
4. Klik Create Service Account
5. Beri nama misalnya: laravel-google-sheet
6. Setelah selesai masuk ke Service Account tersebut
7. Tab Keys
8. Add Key → Create New Key
9. Pilih JSON
10. Download file JSON


2. Aktifkan Google Sheets API
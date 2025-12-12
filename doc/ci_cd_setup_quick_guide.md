# CI/CD Quick Setup — Deploy Laravel via SSH (staging)

Tệp này tóm tắt các bước ngắn gọn để thiết lập CI/CD (GitHub Actions) deploy Laravel lên server bằng SSH.

## 1. Yêu cầu trước
- Có repo trên GitHub.
- Quyền truy cập SSH tới server (VPS / hosting có shell).
- Composer, PHP, git trên server đã cài.

## 2. Trên server
1. Tạo (nếu chưa có) SSH key: `ssh-keygen -t rsa -b 4096 -C "github-deploy"`.
2. Kiểm tra thư mục project (ví dụ `~/public_html`) — đây là đường dẫn deploy.
3. Set remote git sang SSH nếu hiện tại là HTTPS:
   ```sh
   cd ~/public_html
   git remote set-url origin git@github.com:USERNAME/REPO.git
   ```
4. Trust GitHub host:
   ```sh
   ssh-keyscan github.com >> ~/.ssh/known_hosts
   ```
5. Test pull thủ công:
   ```sh
   git pull
   ```

## 3. Trên GitHub
1. **Deploy Key (recommended):** copy `~/.ssh/id_rsa.pub` từ server → GitHub repo → Settings → Deploy keys → Add deploy key (tick _Allow write access_ nếu cần).
2. **Secrets:** thêm `SERVER_IP`, `SERVER_USER`, `SERVER_SSH_KEY` (nội dung `~/.ssh/id_rsa`) vào repo Secrets (Settings → Secrets & variables → Actions).

## 4. File workflow (ví dụ `.github/workflows/deploy.yml`)
- Kích hoạt khi push lên nhánh `staging`.
- Sử dụng `appleboy/ssh-action` để SSH vào server và chạy các lệnh deploy.
- Quan trọng: trong script phải `cd` tới thư mục đúng (ví dụ `/home/youruser/public_html`).

**Ví dụ nội dung (rút gọn):**
```yaml
on:
  push:
    branches: ["staging"]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Deploy to Server
        uses: appleboy/ssh-action@v1.0.0
        with:
          host: ${{ secrets.SERVER_IP }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SERVER_SSH_KEY }}
          script: |
            cd /home/youruser/public_html
            git pull
            composer install --no-interaction --prefer-dist --optimize-autoloader
            php artisan migrate --force
            php artisan optimize
            php artisan cache:clear
            php artisan config:clear
            php artisan route:clear
            php artisan view:clear
            php artisan queue:restart
```

## 5. Test & Debug
- Push 1 commit lên `staging` → kiểm tra GitHub Actions run.
- Nếu lỗi `Permission denied (publickey)` → kiểm tra private key trong Secret và public key đã add làm Deploy Key.
- Nếu lỗi `Host key verification failed` → đảm bảo `ssh-keyscan github.com` đã chạy trên server.
- Nếu `could not read Username for 'https://github.com'` → remote vẫn đang dùng HTTPS, cần đổi sang SSH.

## 6. Lưu ý an toàn
- Giữ private key bí mật (chỉ lưu trong GitHub Secrets).
- Nếu dùng shared hosting, quyền ghi/triển khai có thể cần khác (hỏi nhà cung cấp).

---
Bản tóm tắt này ngắn gọn — nếu cần mình viết chi tiết hơn từng bước (câu lệnh, script kiểm tra log Actions), nói mình biết môi trường server (VPS/shared) và tên user deploy.


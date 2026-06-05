# NoteApp - Güvenlik Açığı Düzeltme Çalışması

Bu proje kapsamında uygulamada bulunan güvenlik açıkları incelenmiş ve gerekli düzenlemeler yapılmıştır. Amaç, kullanıcı bilgilerinin daha güvenli şekilde saklanmasını ve uygulamanın kötü niyetli kullanımlara karşı daha dayanıklı hale getirilmesidir.

## Düzeltilen Güvenlik Açıkları

### 1. Şifre Saklama Yöntemi

İlk sürümde kullanıcı şifreleri yalnızca ilk 5 karakter alınarak SHA256 ile saklanıyordu. Bu yöntem yeterince güvenli olmadığı için kaldırıldı. Yerine PHP'nin sunduğu `password_hash()` ve `password_verify()` fonksiyonları kullanıldı.

### 2. Giriş İşleminde GET Kullanılması

Giriş ekranında kullanıcı adı ve şifre bilgileri GET yöntemi ile gönderiliyordu. Bu durum şifrelerin URL üzerinde görünmesine neden oluyordu. Güvenliği artırmak amacıyla form gönderim yöntemi POST olarak değiştirildi.

### 3. Cookie Tabanlı Kimlik Doğrulama

Kullanıcının oturum bilgisi doğrudan cookie içerisinde tutuluyordu. Bu durum cookie değerinin değiştirilmesi halinde yetkisiz erişimlere yol açabiliyordu. Bu nedenle oturum yönetimi PHP Session sistemi kullanılarak yeniden düzenlendi.

### 4. Not Yetkilendirme Kontrolü (IDOR)

Not düzenleme ve silme işlemlerinde notun ilgili kullanıcıya ait olup olmadığı kontrol edilmiyordu. Yapılan düzenleme ile kullanıcıların yalnızca kendi notları üzerinde işlem yapabilmesi sağlandı.

### 5. Çıkış İşleminde CSRF Riski

Çıkış işlemi doğrudan bir bağlantı üzerinden gerçekleştiriliyordu. Bu durum kötü niyetli yönlendirmelerle kullanıcıların oturumlarının sonlandırılmasına neden olabilirdi. Çıkış işlemi için ek doğrulama mekanizması eklendi.

## Sonuç

Yapılan düzenlemeler sonucunda uygulamanın kullanıcı doğrulama, oturum yönetimi ve veri güvenliği konularındaki eksiklikleri giderilmiş, uygulama daha güvenli bir hale getirilmiştir.

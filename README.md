# Accounting Partner Settlement System

<p align="center">
  <a href="#-العربية">
    <img src="https://img.shields.io/badge/العربية-RTL-blue?style=for-the-badge">
  </a>
  &nbsp;
  <a href="#-english">
    <img src="https://img.shields.io/badge/English-LTR-green?style=for-the-badge">
  </a>
</p>

نظام محاسبة متقدم وخفيف لإدارة العمليات المالية والتسويات بين الشركاء، مناسب للاستخدام الداخلي أو كعرض تجريبي (Demo).

---

## 🇸🇦 العربية

### 📌 الوصف
نظام **Accounting Partner Settlement System** يوفّر:
- إدارة الإيرادات والمصروفات
- حساب الأرباح والخسائر
- تسويات دورية بين الشركاء
- دعم تعدد المستخدمين
- أرشفة كاملة للتسويات السابقة

النظام مصمم ليكون بسيطًا، واضحًا، وقابلًا للتوسع.

---

### 📂 مكونات المشروع
- **dashboard.php**  
  لوحة تحكم متقدمة:
  - دعم كامل للغة العربية (RTL)
  - عرض الأرقام بصيغة LTR
  - واجهة واضحة وسهلة الاستخدام

- **api.php**  
  واجهة برمجية تدعم:
  - تعدد المستخدمين
  - احتساب التسويات بناءً على عدد المستخدمين
  - أرشفة التسويات الدورية

- **data/accounting.sqlite**  
  قاعدة بيانات SQLite جاهزة للتجربة، تحتوي على مستخدمين تجريبيين.

---

### 👤 بيانات الدخول (Demo)
| اسم المستخدم | كلمة المرور | الاسم المعروض |
|--------------|-------------|---------------|
| demo_ali     | 1234        | علي |
| demo_ahmad   | 1234        | أحمد |

> ⚠️ يُنصح بتغيير كلمات المرور قبل الاستخدام الفعلي.

---

### ⚙️ ملاحظة مهمة
إذا كان الدومين مختلفًا عن الإعداد الافتراضي، يجب تعديل المتغير التالي داخل ملف `config.php`:

```php
define('APP_COOKIE_DOMAIN', 'your-domain.com');
```

---

## 🇺🇸 English

### 📌 Description
**Accounting Partner Settlement System** is a lightweight and advanced accounting system designed to manage:
- Revenue and expense management
- Profit and loss tracking
- Periodic partner settlements
- Multi-user support
- Full settlement history and archiving

The system is designed to be simple, clear, and scalable.

---

### 📂 Project Structure
- **dashboard.php**  
  Advanced dashboard interface:
  - Full RTL support for Arabic
  - LTR number formatting
  - Clean and user-friendly design

- **api.php**  
  Backend API supporting:
  - Multiple users
  - Settlement calculations based on user count
  - Archived settlement records

- **data/accounting.sqlite**  
  Ready-to-use SQLite database containing demo users.

---

### 👤 Demo Login Credentials
| Username    | Password | Display Name |
|-------------|----------|--------------|
| demo_ali    | 1234     | Ali |
| demo_ahmad  | 1234     | Ahmad |

> ⚠️ Change passwords before using the system in production.

---

### ⚙️ Important Note
If your domain differs from the default configuration, update the following constant in `config.php`:

```php
define('APP_COOKIE_DOMAIN', 'your-domain.com');
```

---

## 📎 Notes
- This project is intended for **internal, demo, and portfolio purposes**.
- Additional security hardening is recommended before production use.

---

© Internal Accounting System

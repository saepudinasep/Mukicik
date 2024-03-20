# Proyek Mukicik

Proyek ini disimpan di `C:\xampp\htdocs`.

## Menyiapkan Database

### Langkah 1: Buat Database MySQL

Buat database MySQL dengan nama `db_mukicik`.

### Langkah 2: Buat Tabel-tabel Database

#### Tabel User

```sql
CREATE TABLE tb_user (
    UserId INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50),
    UserEmail VARCHAR(50),
    UserPassword VARCHAR(50),
    UserGender VARCHAR(50),
    UserDOB DATE,
    UserProfilePicture VARCHAR(50) DEFAULT NULL
);
```

#### Tabel Kategori (Category)

```sql
CREATE TABLE tb_category (
    CategoryId INT AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(50)
);
```

#### Tabel Produk (Product)

```sql
CREATE TABLE tb_product (
    ProductId INT AUTO_INCREMENT PRIMARY KEY,
    ProductName VARCHAR(50),
    ProductPrice INT,
    ProductImage VARCHAR(50),
    ProductRating FLOAT,
    CategoryId INT,
    FOREIGN KEY (CategoryId) REFERENCES tb_category(CategoryId)
);
```

### Langkah 3: Isi Tabel-tabel Database

#### Isi Data ke Tabel 'tb_user'

```sql
INSERT INTO tb_user (Username, UserEmail, UserPassword, UserGender, UserDOB, UserProfilePicture)
VALUES
    ('John Doe', 'john@example.com', 'password123', 'Male', '1990-05-15', 'profile1.jpg'),
    ('Jane Smith', 'jane@example.com', 'password456', 'Female', '1992-08-20', 'profile2.jpg'),
    ('Alice Johnson', 'alice@example.com', 'password789', 'Female', '1985-12-10', NULL);
```

#### Isi Data ke Tabel 'tb_category'

```sql
INSERT INTO tb_category (CategoryName) VALUES ('Non Electric'),('Electric');
```

#### Isi Data ke Tabel 'tb_product'

```sql
INSERT INTO tb_product (ProductName, ProductPrice, ProductImage, ProductRating, CategoryId)
VALUES
    ('piano', 400000, 'piano.jpg', 4.6, 2),
    ('biola', 200000, 'biola.jpg', 4.3, 1),
    ('mic', 5000, 'mic.jpg', 4.2, 2),
    ('headphone', 10000, 'headphone.jpg', 3.8, 2),
    ('saxophone', 240000, 'saxophone.jpg', 3.8, 1),
    ('guitar', 320000, 'guitar.jpg', 4.2, 1);

```

README.md ini memberikan instruksi yang jelas untuk menyiapkan proyek dan database, termasuk membuat tabel dan mengisinya dengan data sampel.

Jika ada pertanyaan lebih lanjut atau membutuhkan bantuan tambahan, jangan ragu untuk bertanya!

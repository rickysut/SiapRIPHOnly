import os

# Menggunakan direktori saat ini (direktori tempat skrip Python berada)
direktori = os.getcwd()

# Awalan yang akan ditambahkan ke nama file
awalan = '2023_10_05_'

# Loop melalui semua file dalam direktori
for filename in os.listdir(direktori):
    if os.path.isfile(os.path.join(direktori, filename)) and not filename.endswith('.py'):
        # Membuat nama file baru dengan awalan yang ditambahkan
        new_filename = awalan + filename

        # Mendapatkan path lengkap untuk file lama dan baru
        old_filepath = os.path.join(direktori, filename)
        new_filepath = os.path.join(direktori, new_filename)

        # Mengganti nama file
        os.rename(old_filepath, new_filepath)

print("Penggantian nama selesai.")

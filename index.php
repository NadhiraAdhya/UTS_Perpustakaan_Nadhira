<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Sederhana</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('https://i.pinimg.com/564x/e5/77/26/e57726f25c394c9db49bcf287f34f4b9.jpg');
            background-size: cover;
        }
        .container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Perpustakaan Sederhana</h1>

        <!-- Form pencarian buku -->
        <form id="searchForm" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Cari berdasarkan judul, penulis, atau informasi penerbit">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </form>

        <!-- Tabel untuk menampilkan daftar buku -->
        <h2 class="mb-3">Daftar Buku</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Tahun Terbit</th>
                        <th>Informasi Penerbit</th>
                        <th>Status</th>
                        <th>Batas Peminjaman</th>
                        <th>Denda Keterlambatan</th>
                        <th>ISBN Referensi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="bookList">
                    <!-- Daftar buku akan ditampilkan di sini -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>

    <script>
        // Definisikan kelas Book sebagai kelas dasar
        class Book {
            constructor(title, author, year, publisherInfo, status, dueDate, lateFee, id) {
                this.id = id;
                this.title = title;
                this.author = author;
                this.year = year;
                this.publisherInfo = publisherInfo;
                this.status = status;
                this.dueDate = dueDate;
                this.lateFee = lateFee;
            }

            // Metode untuk menampilkan format buku
            displayFormat() {
                console.log('Format: Print');
            }
        }

        // Kelas turunan dari Book dengan tambahan properti dan metode khusus
        class BookReference extends Book {
            constructor(title, author, year, publisherInfo, status, dueDate, lateFee, referenceISBN, id) {
                super(title, author, year, publisherInfo, status, dueDate, lateFee, id);
                this.referenceISBN = referenceISBN;
            }
        }

        // Definisikan kelas Library untuk mengelola daftar buku
        class Library {
            constructor(books) {
                this.books = books;
            }

            // Static method untuk menghitung denda keterlambatan
            static calculateLateFee(book, returnDate) {
                const returnDateObj = new Date(returnDate);
                const dueDateObj = new Date(book.dueDate);

                if (returnDateObj > dueDateObj) {
                    const lateDays = Math.ceil((returnDateObj - dueDateObj) / (1000 * 60 * 60 * 24));
                    const lateFee = lateDays * parseInt(book.lateFee.substring(3));
                    return lateFee;
                } else {
                    return 0;
                }
            }

            // Menambahkan buku baru ke koleksi perpustakaan
            addBook(book) {
                this.books.push(book);
            }

            // Meminjam buku dari perpustakaan berdasarkan ID buku
            borrowBook(bookId) {
                const index = this.books.findIndex(book => book.id === bookId);
                if (index !== -1) {
                    if (this.books[index].status === "Tersedia") {
                        this.books[index].status = "Dipinjam";
                        return true; // Berhasil meminjam buku
                    } else {
                        return false; // Buku sedang tidak tersedia
                    }
                } else {
                    return false; // Buku tidak ditemukan
                }
            }

            // Mengembalikan buku ke perpustakaan berdasarkan ID buku
            returnBook(bookId) {
                const index = this.books.findIndex(book => book.id === bookId);
                if (index !== -1) {
                    if (this.books[index].status === "Dipinjam") {
                        this.books[index].status = "Tersedia";
                        return true; // Berhasil mengembalikan buku
                    } else {
                        return false; // Buku tidak dapat dikembalikan karena belum dipinjam
                    }
                } else {
                    return false; // Buku tidak ditemukan
                }
            }

            // Menghapus buku dari koleksi berdasarkan ID buku
            removeBookById(bookId) {
                this.books = this.books.filter(book => book.id !== bookId);
            }
        }

        // Data buku
        let books = [
            new Book("Ayat-Ayat Cinta", "Habiburrahman El Shirazy", "2004", "Republika", "Tersedia", "01/06/2024", "Rp 0", 1),
            new Book("Bumi Manusia", "Pramoedya Ananta Toer", "1980", "Hasta Mirta", "Tersedia", "27/06/2024", "Rp 0", 2),
            new BookReference("Nanti kita cerita tentang hari ini", "Marchella FP", "2019", "Gramedia Pustaka Utama", "Dipinjam", "06/05/2024", "Rp 10000", "ISBN: 978-6020649597", 3),
            new BookReference("Republik Rakyat Jomblo", "Adhitya Mulya", "2014", "Gagas Media", "Dipinjam", "06/09/2024", "Rp 10000", "ISBN: 978-6020307970", 4),
            new BookReference("Aroma Karsa","Dee Lestari", "2019", "Bentang Pustaka", "Dipinjam", "17/08/2024", "Rp 10000", "ISBN: 978-6022916684", 5),
        ];

        // Membuat objek Library
        let library = new Library(books);

        // Fungsi untuk menampilkan daftar buku
        function displayBooks(booksToShow) {
            $('#bookList').empty(); // Kosongkan tabel buku sebelum menambahkan yang baru
            booksToShow.forEach(function(book){
                $('#bookList').append(`
                    <tr>
                        <td>${book.title}</td>
                        <td>${book.author}</td>
                        <td>${book.year}</td>
                        <td>${book.publisherInfo}</td>
                        <td>${book.status}</td>
                        <td>${book.dueDate}</td>
                        <td>${book.lateFee}</td>
                        <td>
                            ${book.referenceISBN ? book.referenceISBN : '-'}
                        </td>
                        <td>
                            <button onclick="borrowBook(${book.id})" class="btn btn-success">Pinjam</button>
                            <button onclick="returnBook(${book.id})" class="btn btn-warning">Kembalikan</button>
                            <button onclick="deleteBook(${book.id})" class="btn btn-danger">Hapus</button>
                        </td>
                    </tr>
                `);
            });
        }

        // Fungsi untuk melakukan pencarian buku
        function searchBooks(query) {
            return books.filter(book =>
                book.title.toLowerCase().includes(query.toLowerCase()) ||
                book.author.toLowerCase().includes(query.toLowerCase()) ||
                book.publisherInfo.toLowerCase().includes(query.toLowerCase())
            );
        }

        // Event listener untuk form pencarian
        $('#searchForm').submit(function(e){
            e.preventDefault();
            let query = $('#searchInput').val().trim();
            let searchResults = searchBooks(query);
            displayBooks(searchResults);
        });

        // Tampilkan daftar buku saat halaman dimuat
        displayBooks(books);

        // Fungsi untuk meminjam buku
        function borrowBook(bookId) {
            if (library.borrowBook(bookId)) {
                alert("Berhasil meminjam buku!");
                displayBooks(library.books);
            } else {
                alert("Gagal meminjam buku. Buku tidak tersedia atau tidak ditemukan.");
            }
        }

        // Fungsi untuk mengembalikan buku
        function returnBook(bookId) {
            if (library.returnBook(bookId)) {
                alert("Berhasil mengembalikan buku!");
                displayBooks(library.books);
            } else {
                alert("Gagal mengembalikan buku. Buku tidak ditemukan atau belum dipinjam.");
            }
        }

        // Fungsi untuk menghapus buku
        function deleteBook(bookId) {
            if (confirm("Apakah Anda yakin ingin menghapus buku ini?")) {
                library.removeBookById(bookId);
                alert("Buku berhasil dihapus!");
                displayBooks(library.books);
            }
        }
    </script>
</body>
</html>

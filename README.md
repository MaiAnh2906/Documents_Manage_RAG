# Hệ thống quản lý tài liệu và hỏi đáp sử dụng RAG

Hệ thống quản lý tài liệu và hỏi đáp sử dụng **Retrieval-Augmented Generation (RAG)** cho phép người dùng tải lên, quản lý và tìm kiếm tài liệu, đồng thời đặt câu hỏi dựa trên nội dung của các tài liệu đã cung cấp.
Thay vì chỉ dựa vào kiến thức có sẵn của mô hình ngôn ngữ, hệ thống sử dụng quy trình **Retrieval → Augmentation → Generation** để tìm kiếm các đoạn nội dung liên quan trong kho tài liệu trước khi tạo câu trả lời.

---

## Mục tiêu dự án

Dự án được xây dựng nhằm giải quyết bài toán:

* Quản lý tập trung các tài liệu của người dùng.
* Cho phép tải lên và lưu trữ tài liệu.
* Tự động xử lý và phân tích nội dung tài liệu.
* Chuyển nội dung tài liệu thành các vector embedding.
* Lưu trữ embedding trong Vector Database.
* Tìm kiếm các đoạn tài liệu liên quan đến câu hỏi.
* Sử dụng LLM để tạo câu trả lời dựa trên thông tin được truy xuất.
* Hạn chế tình trạng **hallucination** bằng cách cung cấp context từ tài liệu.
* Cho phép người dùng hỏi đáp trực tiếp với kho tài liệu.

---

## Chức năng chính

### Quản lý người dùng

* Đăng ký tài khoản.
* Đăng nhập / đăng xuất.
* Xác thực người dùng.
* Quản lý thông tin cá nhân.
* Phân quyền người dùng.

### Quản lý tài liệu

* Upload tài liệu.
* Xem danh sách tài liệu.
* Xem thông tin chi tiết tài liệu.
* Xóa tài liệu.
* Quản lý tài liệu theo người dùng.

Các loại tài liệu có thể hỗ trợ:

* PDF
* DOCX
* TXT

### Tìm kiếm tài liệu

Hệ thống cho phép tìm kiếm tài liệu dựa trên:

* Từ khóa.
* Nội dung ngữ nghĩa.
* Semantic Search sử dụng Vector Embedding.

### Hỏi đáp sử dụng RAG

Người dùng có thể đặt câu hỏi dựa trên nội dung tài liệu.

Ví dụ:

> **Người dùng:** "Các thành phần chính của hệ thống được đề cập trong tài liệu là gì?"

Hệ thống sẽ:

1. Nhận câu hỏi.
2. Chuyển câu hỏi thành vector embedding.
3. Tìm kiếm các đoạn tài liệu có độ tương đồng cao.
4. Lấy các đoạn nội dung liên quan.
5. Đưa context cùng câu hỏi vào LLM.
6. Sinh câu trả lời.
7. Trả kết quả cho người dùng.

---

# Công nghệ sử dụng

### Frontend

* HTML5
* CSS3
* JavaScript

### Backend

* Python
* PHP

### Artificial Intelligence

* Large Language Model (LLM)
* Embedding Model
* Retrieval-Augmented Generation (RAG)
* Natural Language Processing (NLP)

### Vector Database

* Milvus

### Database

* MySQL

---

# API chính

## Documents

### Upload tài liệu

```http
POST /api/ingest
```

---

## Question Answering

### Đặt câu hỏi

```http
POST /api/chat
```

Request:

```json
{
    "question": "RAG là gì?",
    "document_id": "123"
}
```

Response:

```json
{
    "answer": "RAG là phương pháp kết hợp...",
    "sources": [
        {
            "document": "rag-introduction.pdf",
            "page": 2
        }
    ]
}
```

---

# Ví dụ sử dụng

### Bước 1 — Upload tài liệu

Người dùng tải lên:

```text
rag.pdf
```

### Bước 2 — Hệ thống xử lý

```text
rag.pdf
   ↓
Extract Text
   ↓
Split Chunks
   ↓
Generate Embeddings
   ↓
Store Vectors
```

### Bước 3 — Đặt câu hỏi

```text
"RAG có những thành phần nào?"
```

### Bước 4 — Retrieval

Hệ thống tìm kiếm các chunks liên quan.

```text
Top 1 → Chunk 15
Top 2 → Chunk 23
Top 3 → Chunk 31
```

### Bước 5 — Generate

LLM sử dụng các chunks trên để tạo câu trả lời.

```text
RAG gồm ba thành phần chính:

1. Retrieval
2. Augmentation
3. Generation
```
---

#  Bảo mật

Hệ thống cần đảm bảo:

* Xác thực người dùng.
* Phân quyền truy cập tài liệu.
* Không cho người dùng truy cập tài liệu của người khác.
* Kiểm tra loại và kích thước file upload.
* Validate dữ liệu đầu vào.
* Bảo vệ API key.
* Không lưu API key trực tiếp trong source code.
* Sử dụng biến môi trường cho thông tin nhạy cảm.

---

# Hướng phát triển

Một số tính năng có thể phát triển trong tương lai:

* [ ] Hỗ trợ nhiều loại tài liệu hơn.
* [ ] Chat với nhiều tài liệu cùng lúc.
* [ ] Hiển thị nguồn tham khảo cho từng câu trả lời.
* [ ] Highlight đoạn văn bản được sử dụng để trả lời.
* [ ] Hybrid Search kết hợp Keyword Search và Vector Search.
* [ ] Reranking kết quả retrieval.
* [ ] Query Expansion.
* [ ] Conversation Memory.
* [ ] Streaming response.
* [ ] Phân quyền tài liệu theo nhóm người dùng.
* [ ] Dashboard thống kê.
* [ ] Đánh giá tự động chất lượng RAG.
* [ ] Docker deployment.
* [ ] Cloud deployment.
* [ ] Hỗ trợ multilingual RAG.
* [ ] Caching để giảm thời gian phản hồi và chi phí LLM.

---

# 👨‍💻 Tác giả

**[Phạm Thị Mai Anh & Nguyễn Phương Anh]**

* GitHub: `[https://github.com/MaiAnh2906]`
* Email: `[phamthimaianh29@gmail.com]`

---

# License

Dự án được phát triển phục vụ mục đích **học tập và nghiên cứu**.

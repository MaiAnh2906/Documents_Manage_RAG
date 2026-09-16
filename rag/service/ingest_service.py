from pymilvus import MilvusClient, DataType
from sentence_transformers import SentenceTransformer
import os
from rag.service.config import *
from pypdf import PdfReader
from docx import Document
from langchain_text_splitters import RecursiveCharacterTextSplitter

model = SentenceTransformer("BAAI/bge-m3", local_files_only=True)

def load_document(filepath):
    ext = os.path.splitext(filepath)[1].lower()

    if ext == ".txt":
        with open(filepath, "r", encoding="utf-8") as f:
            return f.read()

    elif ext == ".pdf":
        reader = PdfReader(filepath)
        text = ""

        for page in reader.pages:
            page_text = page.extract_text()
            if page_text:
                text += page_text + "\n"

        return text

    elif ext == ".docx":
        doc = Document(filepath)

        return "\n".join(
            paragraph.text
            for paragraph in doc.paragraphs
        )
    return

text_splitter = RecursiveCharacterTextSplitter(
    chunk_size = 500,
    chunk_overlap = 100,
    add_start_index = True,
    strip_whitespace = True,
    separators= ["\n\n", "\n", " ", ""]
)

def ingest_file(filepath, document_id, user_id):
    vector_db = MilvusClient(DB_PATH)

    if not vector_db.has_collection(COLLECTION):
        schema = MilvusClient.create_schema(
            auto_id=True,
            enable_dynamic_field=True,
        )
        
        schema.add_field(field_name="id", datatype=DataType.INT64, is_primary=True)
        schema.add_field(field_name="vector", datatype=DataType.FLOAT_VECTOR, dim=1024)
        schema.add_field(field_name="text", datatype=DataType.VARCHAR, max_length=65535) # Giới hạn ký tự của chunk
        schema.add_field(field_name="document_id", datatype=DataType.VARCHAR, max_length=255)
        schema.add_field(field_name="user_id", datatype=DataType.VARCHAR, max_length=255)
        schema.add_field(field_name="source", datatype=DataType.VARCHAR, max_length=255) # Lưu tên file
        schema.add_field(field_name="chunk_index", datatype=DataType.INT64)

        # 2. Tạo Index cho vector để tìm kiếm nhanh
        index_params = vector_db.prepare_index_params()
        index_params.add_index(
            field_name="vector",
            metric_type="COSINE", 
            index_type="AUTOINDEX"
        )
        vector_db.create_collection(
        collection_name=COLLECTION,
        schema=schema,
        index_params=index_params
    )

    vector_db.load_collection(COLLECTION)
    text = load_document(filepath)

    if not text.strip():
        raise Exception("Không đọc được nội dung tài liệu.")

    chunks = text_splitter.split_text(text)

    if len(chunks) == 0:
        raise Exception("Không có dữ liệu để embedding.")

    embeddings = model.encode(chunks, batch_size=32, show_progress_bar=True)

    data = []

    for index, (chunk, emb) in enumerate(zip(chunks, embeddings)):

        data.append({
            "vector": emb.tolist(),
            "text": chunk,
            "document_id": str(document_id),
            "chunk_index": index,
            "user_id": str(user_id),
            "source": os.path.basename(filepath)
        })

    vector_db.insert(
        collection_name=COLLECTION,
        data=data
    )
    print("===== INGEST SUCCESS =====")
    print(f"Document ID: {document_id}")
    print(f"Chunks: {len(data)}")

    return len(data)
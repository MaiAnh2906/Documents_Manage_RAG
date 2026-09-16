from sentence_transformers import SentenceTransformer
from google import genai
from pymilvus import MilvusClient
from rag.service.config import DB_PATH, COLLECTION

client = genai.Client(api_key="AIzaSyAghwDsTzq-ppzRa61x0ydYNrBW-7kX3O8")
vector_db = MilvusClient(DB_PATH)
model = SentenceTransformer("BAAI/bge-m3", local_files_only=True)

def retrieve(query, user_id, top_n = 3):
    query_embedding = model.encode(query)

    if not vector_db.has_collection(COLLECTION):
        return []

    vector_db.load_collection(COLLECTION)
    
    search_results = vector_db.search(
        collection_name=COLLECTION,
        data=[query_embedding],
        limit=top_n,
        # ĐIỀU KIỆN LỌC CỐT LÕI: Chỉ tìm các chunk thuộc về user_id này
        filter=f'user_id == "{user_id}"', 
        output_fields=["text", "source", "document_id"] # Yêu cầu trả về nội dung và tên file
    )
    return search_results[0] if search_results else []

def generate_answer(input_query: str, user_id: str) -> str:

    retrieved_knowledge = retrieve(input_query, user_id)

    context_chunks = []
    document_ids = []
    seen_document_ids = set()
    for hit in retrieved_knowledge:
        entity = hit["entity"]

        text = entity.get("text", "")
        source = entity.get("source", "")
        document_id = str(entity.get("document_id", "")).strip()

        if document_id and document_id not in seen_document_ids:
            document_ids.append(document_id)
            seen_document_ids.add(document_id)
        context_chunks.append(
            f"[SOURCE_FILE: {source}]\n"
            f"[DOCUMENT_ID: {document_id}]\n"
            f"{text}"
        )

    newline = "\n"
    prompt = f"""
    Bạn là AI assistant. Chỉ trả lời dựa trên context dưới đây. 
    Nhiệm vụ:
    - Chỉ trả lời dựa trên CONTEXT được cung cấp.
    - Không tự bịa thông tin hoặc nguồn tài liệu.
    - Nếu CONTEXT không chứa thông tin cần thiết, trả lời đúng:
    "Không tìm thấy thông tin."

    QUY TẮC TRÍCH DẪN:
    - Nếu câu trả lời sử dụng thông tin từ một hoặc nhiều tài liệu,
    bắt buộc thêm citation ở cuối câu trả lời.
    - Citation phải dùng ĐÚNG tên file nằm trong [SOURCE_FILE: ...].
    - Không được tự thay đổi tên file.
    - Không được tạo HTML.
    - Không được tạo <a>, href hoặc URL.
    - Format chính xác:
    [Trích từ file: TEN_FILE]
    - Nếu dùng nhiều file, ghi mỗi file một citation riêng.
    - Chỉ trích dẫn những file thực sự được sử dụng để trả lời.
    Context:
    {newline.join(context_chunks)}
    Question:
    {input_query}
    """

    response = client.models.generate_content(
        model="gemini-2.5-flash",
        contents=prompt
    )
    answer = (response.text or "").strip()

    if not answer:
        answer = "Không tìm thấy thông tin."

    # Marker nội bộ để PHP biết document_id nào đã được retrieval.
    # PHP sẽ xóa marker trước khi hiển thị và lấy name/path từ bảng files.
    if document_ids and answer != "Không tìm thấy thông tin.":
        marker = "[[RAG_DOCUMENT_IDS:" + ",".join(document_ids) + "]]"
        answer = answer + "\n\n" + marker

    return answer
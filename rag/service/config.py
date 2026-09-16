from pathlib import Path
BASE_DIR = Path(__file__).resolve().parents[2]
DB_PATH = str(BASE_DIR / "rag" / "database" / "milvus.db")
COLLECTION = "demo_collection"
MODEL_NAME = "BAAI/bge-m3"
# folder = "D:/AI/T3/data"
print("Hello")
from flask import Flask, request, jsonify
from rag.service.ingest_service import ingest_file

app = Flask(__name__)

@app.post("/ingest")
def ingest():

    try:

        data = request.json

        count = ingest_file(
            data["path"],
            data["document_id"],
            data["user_id"]
        )

        print(f"Ingest thành công {count} chunks.")

        return {
            "success": True,
            "chunks": count
        }

    except Exception as e:

        print("Lỗi:", e)

        return {
            "success": False,
            "message": str(e)
        },500
if __name__ == "__main__":
    app.run(
        host="127.0.0.1",
        port=8000,
        debug=True
    )
print("OK")
from flask import Flask, request, jsonify
from rank_bm25 import BM25Okapi
from nltk.tokenize import word_tokenize
import nltk
import pandas as pd
import string
import json

# Download tokenizer dari NLTK
nltk.download('punkt')

# Inisialisasi aplikasi Flask
app = Flask(__name__)

# Load dataset CSV ke dalam DataFrame
df = pd.read_csv('final_dataset.csv')

# Preprocessing: hapus tanda baca, ubah ke lowercase, dan tokenize setiap dokumen
translator = str.maketrans('', '', string.punctuation)
documents = df['Input'].astype(str).tolist()
tokenized_corpus = [word_tokenize(doc.lower().translate(translator)) for doc in documents]

# Inisialisasi BM25 dengan korpus yang sudah ditokenisasi
bm25 = BM25Okapi(tokenized_corpus)

# Ambil semua term unik dari korpus untuk kebutuhan autocomplete
unique_terms = sorted(set(word for doc in tokenized_corpus for word in doc))

# Endpoint untuk melakukan pencarian jurnal dengan BM25
@app.route('/search', methods=['GET'])
def search():
    query = request.args.get('keyword', '').lower()  # Ambil query dari parameter URL
    year = request.args.get('year', '')              # Ambil filter tahun

    if not query:
        return jsonify([])  # Kalau query kosong, kembalikan array kosong

    # Tokenisasi query setelah hilangkan tanda baca
    tokenized_query = word_tokenize(query.translate(translator))

    # Hitung skor BM25 dan urutkan indeks berdasarkan skor tertinggi
    scores = bm25.get_scores(tokenized_query)
    ranked_indices = sorted(range(len(scores)), key=lambda i: scores[i], reverse=True)

    results = []
    for idx in ranked_indices:
        row = df.iloc[idx]
        try:
            parsed_output = json.loads(row['Final_Output'])  # Parse kolom hasil jika bentuknya JSON
        except:
            parsed_output = {}

        # Filter berdasarkan tahun jika diisi
        if year and str(parsed_output.get('year', '')).strip() != str(year).strip():
            continue

        # Tambahkan hasil ke list respons
        results.append({
            'input': row['Input'],
            'output': row['Final_Output'],
            'score': float(scores[idx])
        })

        # Maksimum 10 hasil saja ditampilkan
        if len(results) == 10:
            break

    return jsonify(results)

# Endpoint autocomplete
@app.route('/autocomplete', methods=['GET'])
def autocomplete():
    keyword = request.args.get('term', '').lower()
    if not keyword:
        return jsonify([])

    # Ambil maksimal 10 term yang diawali dengan keyword
    suggestions = [term for term in unique_terms if term.startswith(keyword)][:10]
    return jsonify(suggestions)

# Jalankan Flask server
if __name__ == '__main__':
    app.run(debug=True, port=5000)

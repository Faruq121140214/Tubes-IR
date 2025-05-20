from flask import Flask, request, jsonify
from rank_bm25 import BM25Okapi
from nltk.tokenize import word_tokenize
import nltk
import pandas as pd
import string
import json

nltk.download('punkt')

app = Flask(__name__)

# Load dataset
df = pd.read_csv('final_dataset.csv')

# Preprocessing & tokenisasi dokumen
translator = str.maketrans('', '', string.punctuation)
documents = df['Input'].astype(str).tolist()
tokenized_corpus = [word_tokenize(doc.lower().translate(translator)) for doc in documents]
bm25 = BM25Okapi(tokenized_corpus)

# Buat list term unik untuk autocomplete
unique_terms = sorted(set(word for doc in tokenized_corpus for word in doc))

@app.route('/search', methods=['GET'])
def search():
    query = request.args.get('keyword', '').lower()
    year = request.args.get('year', '')

    if not query:
        return jsonify([])

    tokenized_query = word_tokenize(query.translate(translator))
    scores = bm25.get_scores(tokenized_query)
    ranked_indices = sorted(range(len(scores)), key=lambda i: scores[i], reverse=True)

    results = []
    for idx in ranked_indices:
        row = df.iloc[idx]
        try:
            parsed_output = json.loads(row['Final_Output'])
        except:
            parsed_output = {}

        # Filter tahun jika diisi
        if year and str(parsed_output.get('year', '')).strip() != str(year).strip():
            continue

        results.append({
            'input': row['Input'],
            'output': row['Final_Output'],
            'score': float(scores[idx])
        })

        if len(results) == 10:
            break

    return jsonify(results)

@app.route('/autocomplete', methods=['GET'])
def autocomplete():
    keyword = request.args.get('term', '').lower()
    if not keyword:
        return jsonify([])

    # Gunakan startswith agar lebih relevan
    suggestions = [term for term in unique_terms if term.startswith(keyword)][:10]
    return jsonify(suggestions)

if __name__ == '__main__':
    app.run(debug=True, port=5000)

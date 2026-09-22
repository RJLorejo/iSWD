import json
from pathlib import Path

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


class KnowledgeAssistant:
    def __init__(self):
        self.knowledge_path = (
            Path(__file__).resolve().parents[2]
            / "data"
            / "knowledge_base.json"
        )

        self.documents = self._load_documents()

        self.vectorizer = TfidfVectorizer(
            lowercase=True,
            stop_words="english"
        )

        self.document_matrix = self.vectorizer.fit_transform(
            [
                f"{document['title']} {document['content']}"
                for document in self.documents
            ]
        )

    def _load_documents(self):
        with open(
            self.knowledge_path,
            "r",
            encoding="utf-8"
        ) as file:
            return json.load(file)

    def search(self, question: str, limit: int = 3):
        question_vector = self.vectorizer.transform([question])

        scores = cosine_similarity(
            question_vector,
            self.document_matrix
        )[0]

        ranked_indexes = scores.argsort()[::-1][:limit]

        results = []

        for index in ranked_indexes:
            results.append({
                "id": self.documents[index]["id"],
                "title": self.documents[index]["title"],
                "content": self.documents[index]["content"],
                "score": round(float(scores[index]), 4)
            })

        return results

    def answer(self, question: str):
        results = self.search(question)

        if not results:
            return {
                "answer": "I could not find relevant information in the approved knowledge base.",
                "confidence": 0,
                "sources": []
            }

        best = results[0]

        if best["score"] < 0.15:
            return {
                "answer": (
                    "I could not find enough relevant information in "
                    "the approved knowledge base to answer that question. "
                    "Please submit a complaint or contact Customer Service "
                    "for assistance."
                ),
                "confidence": best["score"],
                "sources": results
            }

        return {
            "answer": best["content"],
            "confidence": best["score"],
            "sources": results
        }

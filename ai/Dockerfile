FROM python:3.12-slim

WORKDIR /app/project

COPY requirements.txt /app/

RUN pip install --no-cache-dir -r /app/requirements.txt

COPY . /app/project/ai

ENV PYTHONPATH=/app/project

EXPOSE 8001

CMD ["uvicorn", "ai.main:app", "--host", "0.0.0.0", "--port", "8001"]

"""pubM Flask development runtime (PHP is production authoritative)."""

from __future__ import annotations

from datetime import datetime, timezone

from flask import Flask, jsonify

app = Flask(__name__)


@app.get("/health")
def health():
    return jsonify(
        {
            "status": "ok",
            "module": "pubM",
            "runtime": "flask-dev",
            "time": datetime.now(timezone.utc).isoformat(),
        }
    )


@app.get("/api/copilot/dashboard")
def copilot_dashboard():
    return jsonify(
        {
            "module": "pubM",
            "runtime": "flask-dev",
            "publication_count": 0,
            "published_count": 0,
            "renewal_due_count": 0,
            "health_issue_count": 0,
            "note": "Use PHP production API for live data",
        }
    )


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5011, debug=True)

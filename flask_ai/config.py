import os

# Flask configuration
DEBUG = os.getenv('FLASK_DEBUG', 'True') == 'True'
SECRET_KEY = os.getenv('FLASK_SECRET_KEY', 'your-secret-key')

# API configuration
API_VERSION = '1.0'
API_PREFIX = '/api/v1'

# Database configuration (if needed)
DATABASE_URI = os.getenv('DATABASE_URI', 'sqlite:///app.db')

# Redis configuration (for state management)
REDIS_URL = os.getenv('REDIS_URL', 'redis://localhost:6379/0')

# API keys and credentials
OPENAI_API_KEY = os.getenv('OPENAI_API_KEY', 'your-openai-api-key')

# LLM configuration
DEFAULT_MODEL = os.getenv('DEFAULT_MODEL', 'gpt-4o-mini')
ADVANCED_MODEL = os.getenv('ADVANCED_MODEL', 'gpt-4o-mini')
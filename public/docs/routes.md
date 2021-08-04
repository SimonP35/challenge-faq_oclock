# Les routes

| Routes | Nom de la route | Méthodes (HTTP) |
|---|---|---|
| `/api/questions`  | `api_questions_list`  | `GET`  |
| `/api/questions/add`  | `api_questions_add`  | `POST` |
| `/api/questions/{id}`  | `api_questions_show`  | `GET`  |
| `/api/tags`  | `api_tags_list`  | `GET`  |
| `/api/tags/{id}`  | `api_tags_show`  | `GET`  |

## Les contrôleurs

| Routes | Controller | ->méthode() |
|---|---|---|
| `/api/questions`  |  `App\Controller\Api\QuestionController` | `list()`  |
| `/api/questions/{id}`  |  `App\Controller\Api\QuestionController` | `show()`  |
| `/api/questions/add`  |  `App\Controller\Api\QuestionController` | `add()`  |
| `/api/tags`  |  `App\Controller\Api\TagController` | `list()`  |
| `/api/tags/{id}/questions`  |  `App\Controller\Api\TagController` | `show()`  |

### Correction cours

| Routes                   | Nom de la route        | Controller   | Methodes (HTTP) | Méthode             |
| ------------------------ | ---------------------- | ------------ | --------------- | ------------------- |
| /api/questions           | api_questions_get      | Api\Question | GET             | get()               |
| /api/questions/{id}      | api_questions_get_item | Api\Question | GET             | getItem()           |
| /api/questions           | api_questions_add      | Api\Question | POST            | add()               |
| /api/tags                | api_tags_get           | Api\Tag      | GET             | get()               |
| /api/tags/{id}/questions | api_questions_by_tags  | Api\Tag      | GET             | getQuestionsByTag() |
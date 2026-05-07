#Response scheme

{
  "type": "object",
  "description": "Метод получения информации о врачах с пагинацией",
  "properties": {
    "links": {
      "allOf": [
        {
          "type": "object",
          "description": "Ссылки",
          "properties": {
            "next": {
              "type": [
                "string",
                "null"
              ],
              "format": "uri",
              "description": "Следующая страница"
            },
            "previous": {
              "type": [
                "string",
                "null"
              ],
              "format": "uri",
              "description": "Предыдущая страница"
            }
          },
          "required": [
            "next",
            "previous"
          ]
        }
      ],
      "description": "Ссылки на другие страницы"
    },
    "count": {
      "type": "integer",
      "minimum": 0,
      "description": "Общее количество объектов"
    },
    "num_pages": {
      "type": "integer",
      "minimum": 0,
      "description": "Общее количество страниц"
    },
    "data": {
      "type": "array",
      "items": {
        "type": "object",
        "description": "Объект врача",
        "properties": {
          "id": {
            "type": "integer",
            "minimum": 1,
            "description": "Идентификатор врача"
          },
          "efio": {
            "type": "string",
            "description": "ФИО врача"
          },
          "specialities": {
            "type": "array",
            "items": {
              "type": "integer",
              "minimum": 1
            },
            "description": "Cписок идентификаторов специальностей врача"
          },
          "lpus": {
            "type": "array",
            "items": {
              "type": "integer",
              "minimum": 1
            },
            "description": "Список идентификаторов клиник, в которых работает врач"
          },
          "rating": {
            "type": "object",
            "description": "Объект рейтинга",
            "properties": {
              "stars": {
                "type": "number",
                "format": "double",
                "description": "Звезды"
              },
              "public": {
                "type": "number",
                "format": "double",
                "description": "Рейтинг врача"
              }
            },
            "required": [
              "public",
              "stars"
            ]
          },
          "education_and_experience": {
            "type": "object",
            "description": "Объект образования",
            "properties": {
              "category": {
                "allOf": [
                  {
                    "enum": [
                      "без категории",
                      "2 категория",
                      "1 категория",
                      "высшая категория"
                    ],
                    "type": "string",
                    "description": "* `без категории` - без категории\n* `2 категория` - 2 категория\n* `1 категория` - 1 категория\n* `высшая категория` - высшая категория"
                  }
                ],
                "description": "Категория\n\n* `без категории` - без категории\n* `2 категория` - 2 категория\n* `1 категория` - 1 категория\n* `высшая категория` - высшая категория"
              },
              "education": {
                "type": "string",
                "description": "Образование"
              },
              "experience": {
                "type": "string",
                "description": "Стаж"
              }
            },
            "required": [
              "category",
              "education",
              "experience"
            ]
          },
          "avatar": {
            "type": "string",
            "format": "uri",
            "description": "Изображение врача"
          },
          "avatar_300": {
            "type": "string",
            "format": "uri",
            "description": "Изображение врача 300x300"
          },
          "review_count": {
            "type": "integer",
            "minimum": 0,
            "description": "Количество отзывов"
          },
          "reviews": {
            "type": "array",
            "items": {
              "type": "integer"
            },
            "description": "Поле устарело. Всегда пустой список"
          },
          "doctor_url": {
            "type": "string",
            "format": "uri",
            "description": "Ссылка на отзывы врача"
          },
          "prices": {
            "type": "array",
            "items": {
              "type": "object",
              "description": "Цена приема врача в клинике по специальности",
              "properties": {
                "speciality_id": {
                  "type": "integer",
                  "minimum": 1,
                  "description": "ID специальности"
                },
                "lpu_id": {
                  "type": "integer",
                  "minimum": 1,
                  "description": "ID клиники"
                },
                "price": {
                  "type": [
                    "integer",
                    "null"
                  ],
                  "minimum": 0,
                  "description": "Стоимость приема. 0 - бесплатный прием. null - цена не указана."
                }
              },
              "required": [
                "lpu_id",
                "price",
                "speciality_id"
              ]
            },
            "description": "Цены на приемы"
          },
          "allowed_age": {
            "type": "array",
            "items": {
              "type": "object",
              "description": "Возраст для приема врача в клинике по специальности",
              "properties": {
                "speciality_id": {
                  "type": "integer",
                  "minimum": 1,
                  "description": "ID специальности"
                },
                "lpu_id": {
                  "type": "integer",
                  "minimum": 1,
                  "description": "ID клиники"
                },
                "min": {
                  "type": "integer",
                  "maximum": 100,
                  "minimum": 0,
                  "description": "Минимальный возраст пациента для приема"
                },
                "max": {
                  "type": "integer",
                  "maximum": 100,
                  "minimum": 0,
                  "description": "Максимальный возраст пациента для приема"
                }
              },
              "required": [
                "lpu_id",
                "max",
                "min",
                "speciality_id"
              ]
            },
            "description": "Возраст для приема"
          }
        },
        "required": [
          "allowed_age",
          "efio",
          "id",
          "lpus",
          "prices",
          "review_count",
          "specialities"
        ]
      }
    }
  },
  "required": [
    "count",
    "data",
    "links",
    "num_pages"
  ]
}
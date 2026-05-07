#Response scheme

{
  "type": "object",
  "description": "Метод получения расписания с пагинацией v2",
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
        "description": "Расписание v2",
        "properties": {
          "lpu_id": {
            "type": "integer",
            "description": "Идентификатор клиники"
          },
          "doctor_id": {
            "type": "integer",
            "description": "Идентификатор врача"
          },
          "specialities": {
            "type": "array",
            "items": {
              "type": "integer"
            },
            "description": "Список идентификаторов специальностей"
          },
          "prices": {
            "type": "array",
            "items": {
              "type": "object",
              "description": "Цена приема врача по специальности",
              "properties": {
                "speciality_id": {
                  "type": "integer",
                  "minimum": 1,
                  "description": "ID специальности"
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
              "description": "Возраст для приема врача по специальности",
              "properties": {
                "speciality_id": {
                  "type": "integer",
                  "minimum": 1,
                  "description": "ID специальности"
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
                "max",
                "min",
                "speciality_id"
              ]
            },
            "description": "Возраст для приема"
          },
          "cells": {
            "type": "array",
            "items": {
              "type": "object",
              "description": "Ячейка",
              "properties": {
                "dt_start": {
                  "type": "string",
                  "description": "Дата начала в формате %Y-%m-%d %H:%M"
                },
                "dt_end": {
                  "type": "string",
                  "description": "Дата окончания в формате %Y-%m-%d %H:%M"
                }
              },
              "required": [
                "dt_end",
                "dt_start"
              ]
            }
          }
        },
        "required": [
          "allowed_age",
          "cells",
          "doctor_id",
          "lpu_id",
          "prices",
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
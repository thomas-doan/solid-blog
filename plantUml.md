@startuml

package App.Core.EntityManager {
    class EntityManager {
        - primaryKey: string
        - primaryTable: string
        - fields: EntityAttributeInterface[]
        - relations: array
        - result: array
        - historyQuery: array
        - queryBuilder: QueryBuilderInterface
        + __construct(primaryTable: string)
        + getHistoryQuery(): array
        + saveQuery(functionName: string, query: string | array, params: array): void
        + getLastQuery(): array
        + findOne(id: array | int): void
        + find(params: array): void
        + save(dto: DTOInterface): void
        + modify(id: int, params: array): void
        + populate(relationTableName: string, params: array): void
        + isUniqueField(field: string, value: string): bool
    }
    class EntityAttribute implements EntityAttributeInterface {
        - COLUMN_NAME: string
        - DATA_TYPE: string
        - CHARACTER_MAXIMUM_LENGTH: int | null
        - COLUMN_DEFAULT: string | null
        - IS_NULLABLE: string
        - COLUMN_KEY: string | null
        - EXTRA: string | null
        + __construct(data: array)
        + __get(name: string)
        + __isset(name: string)
        + __toString(): string
        + toArray(): array
        + toJson(): string
        + getName(): string
        + getType(): string
        + getCompatibilityType(): string
        + getMaxLength(): int
        + isNullable(): bool
        + isPrimary(): bool
        + isAutoIncrement(): bool
        + isUnique(): bool
        + isMultiple(): bool
        + isForeign(): bool
        + isDefault(): bool
    }

    interface EntityAttributeInterface {
        + __construct(data: array)
        + __get(name: string): mixed
        + __isset(name: string): bool
        + __toString(): string
        + toArray(): array
        + toJson(): string
        + getName(): string
        + getType(): string
        + getCompatibilityType(): string
        + getMaxLength(): int
        + isNullable(): bool
        + isPrimary(): bool
        + isAutoIncrement(): bool
        + isUnique(): bool
        + isMultiple(): bool
        + isForeign(): bool
        + isDefault(): bool
    }
}

package App.Core.DataTransferObjectManager {
    class DTOManager {
        - pilote: DTOPilote[]
        - dataToTransfer: array
        - entity: EntityManager
        - format: DTODecorator
        + __construct(entity: App.Core.EntityManager.EntityManager)
        + __process(entity: DTOInterface)
        - __sanityzeDecorator(contrainte: string): mixed
        - __parseAnnotations(docComment: string, matchesDecorator: string[]): array
        - __setHistoryMethod(attribute: string, methodName: string, value: mixed): void
        - __getHistoryMethod(attribute: string): mixed[]
        - isUnique(piloteProperty: DTOPilote): void
        - isMail(piloteProperty: DTOPilote): void
        - isPassword(piloteProperty: DTOPilote): void
        - minLength(piloteProperty: DTOPilote, contrainte: int): void
        - maxLength(piloteProperty: DTOPilote, contrainte: int): void
        - defineRegex(piloteProperty: DTOPilote, contraintes: string): void
        - type(piloteProperty: DTOPilote, contrainte: string): void
        - default(piloteProperty: DTOPilote, contrainte: mixed): void
        - tableau(piloteProperty: DTOPilote): void
        - tableauKeyValue(piloteProperty: DTOPilote): void
    }

    interface DTOInterface {
        + __construct()
    }
}

namespace App.Core.DataTransferObjectManager {
    class DTOPilote {
        - name: string
        - value: mixed
        - methodes: array
        + __construct(name: string, value: mixed)
        + addMethode(attribute: string, value: mixed): void
        + getField(): string
        + getValue(): mixed
        + getMethodes(): array
        + addHistory(methodName: string, value: mixed): void
        + getHistorys(): array
    }

    class DTODecorator {
        - manager: DTOManager
        + __construct(manager: DTOManager)
        + insert()
        + find()
        + delete()
        + update()
    }

    interface DTOInterface {
        + getter(): mixed
        + setter(value: mixed): void
    }
}


package App.Core.Database {


    interface DatabaseInterface {
    + query(statement: string, attributes: array = null, one: bool = false)
    + __construct()
    }

    class DatabaseSQL implements DatabaseInterface {
    + query(statement: string, attributes: array = null, one: bool = false)
    + __construct()
    }

    class DatabaseMongo implements DatabaseInterface {
    + query(statement: string, attributes: array = null, one: bool = false)
    + __construct()
    }

    class DatabaseManager implements DatabaseManagerInterface {
        {static} getInfoTable(table: string): array
        {static} getRelationsTable(table: string): array
        {static} isUnique(table: string, field: string, value: mixed): bool
    }

    interface DatabaseManagerInterface {
        + getInfoTable(table: string): array
        + getRelationsTable(table: string): array
        + isUnique(table: string, field: string, value: string): bool
    }

}

package App.Core.QueryBuilder {

    interface QueryBuilderInterface {
        + select(params: array): QueryBuilderInterface
        + update(table: string, params: array): QueryBuilderInterface
        + delete(table: string): QueryBuilderInterface
        + insert(table: string, params: array): QueryBuilderInterface
        + from(table: string): QueryBuilderInterface
        + where(condition: array): QueryBuilderInterface
        + join(Entity: string, foreignKeys: array): QueryBuilderInterface
    }

    class QueryBuilderSQL implements QueryBuilderInterface{
        - db
        - query
        - select
        - close
        - join
        - table
        + __construct()
        + sendQuery(isOnly: bool = false)
        + select(params = null)
        + update(table: string, params: array)
        + delete(table: string)
        + insert(table: string, params: array)
        + from(table: string)
        + where(condition: array)
        + join(Entity: string, foreignKeys: array)
    }

        class QueryBuilderMongo implements QueryBuilderInterface{
        - db
        - query
        - select
        - close
        - join
        - table
        + __construct()
        + sendQuery(isOnly: bool = false)
        + select(params = null)
        + update(table: string, params: array)
        + delete(table: string)
        + insert(table: string, params: array)
        + from(table: string)
        + where(condition: array)
        + join(Entity: string, foreignKeys: array)
    }
}

package App.Entity {
    package App.Entity.User {
        class UserEntity implements App.Core.EntityManager.EntityInterface {
            - id : int
            - password : string
            - firstname : string
            - lastname : string
            - role : RoleEntity
            - posts : PostEntity[]
            - comments : CommentEntity[]

            + __construct()
            + findUserById(id : int)
            + connect()
            + getMe()
            + findAllUser( DTOUserPublic : App.Core.DataTransferObjectManager.DTOInterface)
            + findOnUser()
        }

        package App.Entity.User.DTO {
            class DTOInsertUser implements App.Core.DataTransferObjectManager.DTOInterface {
                - id : int
                - password : string
                - firstname : string
                - lastname : string
                - role : RoleEntity
            }

            class DTOgetMeUser implements App.Core.DataTransferObjectManager.DTOInterface {
                - id : int
                - firstname : string
                - lastname : string
                - role : RoleEntity
            }

            class DTOgetUserPublic implements App.Core.DataTransferObjectManager.DTOInterface {
                - id : int
                - firstname : string
                - lastname : string
            }
        }

    }
    package App.Entity.Post {
        class PostEntity implements App.Core.EntityManager.EntityInterface {
            - id : int
            - title : string
            - content : string
            - updatedAt : datetime
            - createdAt : datetime
            - autor : UserEntity
            - category : CategoryEntity

        }

        package App.Entity.Post.DTO {
            class DTOgetAllPost implements App.Core.DataTransferObjectManager.DTOInterface {
                            - id : int
            - title : string
            - content : string
            - updatedAt : datetime
            - createdAt : datetime
            - autor : UserEntity
            - category : CategoryEntity
            }
        }
    }

    package App.Entity.Category {
        class CategoryEntity implements App.Core.EntityManager.EntityInterface {
            - id : int
            - title : string
            - posts : PostEntity[]
        }

        package App.Entity.Category.DTO {
            class DTOCreateCategory implements App.Core.DataTransferObjectManager.DTOInterface {
                - title : string
            }
        }
    }

    package App.Entity.Comment {
        class CommentEntity implements App.Core.EntityManager.EntityInterface {
        - id : int
        - content : string
        - createdAt : datetime
        - post : PostEntity
        - user : UserEntity
        }

        package App.Entity.Comment.DTO {
            class DTOCreateCategory implements App.Core.DataTransferObjectManager.DTOInterface {
                - content : string
                - createdAt : datetime
                - post : PostEntity
                - user : UserEntity
            }
        }

    }
}



App.Core.EntityManager.EntityManager ..* App.Core.QueryBuilder.QueryBuilderInterface
App.Core.EntityManager.EntityInterface ..* App.Core.EntityManager.EntityManager
App.Core.EntityManager.EntityInterface ..* App.Core.DataTransferObjectManager.DTOManager
App.Core.EntityManager.EntityManager --* App.Core.EntityManager.EntityAttributeInterface
App.Core.EntityManager.EntityManager --* App.Core.Database.DatabaseManagerInterface
App.Core.Database.DatabaseManager --* App.Core.QueryBuilder.QueryBuilderInterface
App.Core.EntityManager.EntityManager --o App.Core.DataTransferObjectManager.DTOManager
App.Core.QueryBuilder.QueryBuilderSQL --*  App.Core.Database.DatabaseInterface
App.Core.QueryBuilder.QueryBuilderMongo --*  App.Core.Database.DatabaseInterface
App.Core.DataTransferObjectManager.DTOManager --* App.Core.DataTransferObjectManager.DTODecorator
App.Core.DataTransferObjectManager.DTOManager --* App.Core.DataTransferObjectManager.DTOPilote
App.Core.DataTransferObjectManager.DTOManager *--o App.Core.DataTransferObjectManager.DTOInterface
App.Entity.User.UserEntity --|> App.Core.EntityManager.EntityManager
App.Entity.User.UserEntity *--o App.Entity.Post.PostEntity
App.Entity.User.UserEntity *--o App.Entity.Comment.CommentEntity
App.Entity.Post.PostEntity --|> App.Core.EntityManager.EntityManager
App.Entity.Post.PostEntity *--o App.Entity.Category.CategoryEntity
App.Entity.Category.CategoryEntity --|> App.Core.EntityManager.EntityManager
App.Entity.Comment.CommentEntity --|> App.Core.EntityManager.EntityManager

@enduml
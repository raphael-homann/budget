import {fr as frCore} from 'vuetify/lib/locale'

export const fr = {
    $vuetify: {
        ...frCore
    },
    fse: {
        menu: {
            daily: 'Quotidien',
            workflow: 'Flux de travail',
            todos: 'Tâches',
        },
        entities: {
            Todo: {
                listing: {
                    add: 'Ajouter une tâche',
                    edit: 'Modifier une tâche',
                },
                fields: {
                    title: 'Titre',
                    finished: 'Fait',
                    count: 'nombre',
                    user: 'Utilisateur',
                    description: 'Description',
                }
            }
        }
    },
};

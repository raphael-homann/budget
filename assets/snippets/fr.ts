import {fr as frCore} from 'vuetify/lib/locale'

export const fr = {
    $vuetify: {
        ...frCore
    },
    budget: {
        app: {
            title: 'Gestion de budget',
        },
        menu: {
            daily: 'Quotidien',
            workflow: 'Flux de travail',
            todos: 'Tâches',
        },
        entities: {
            Budget:{
                listing:{
                    edit: 'Editer budget',
                    add: 'Ajouter budget',
                },
                fields:{
                    name: 'Nom',
                    description: 'Description',
                }
            },
            Envelope:{
                listing:{
                    add: 'Ajouter enveloppe',
                    edit: 'Editer enveloppe',
                },
                fields:{
                    name: 'Nom',
                    description: 'Description',
                }
            }
        }
    },
};

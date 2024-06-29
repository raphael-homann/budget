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
            home: 'Accueil',
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

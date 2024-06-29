import {en as enCore} from 'vuetify/lib/locale'


export const en = {
    $vuetify: {
        ...enCore,
    },
    budget: {
        app: {
            title: 'Budget management',
        },
        menu: {
            home: 'Home',
        },
        entities: {
            Budget:{
                listing:{
                    edit: 'Edit budget',
                    add: 'Add budget',
                },
                fields:{
                    name: 'Name',
                    description: 'Description',
                }
            }
        }
    },
}

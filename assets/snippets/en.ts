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
            daily: 'Daily',
            workflow: 'Workflow',
            todos: 'Todos',
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

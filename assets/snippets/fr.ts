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
            DetectionMask:{
                listing:{
                    edit: 'Editer détection',
                    add: 'Ajouter détection',
                },
                fields:{
                    mask: 'masque',
                    score: 'score',
                    active: 'active',
                    category: 'catégorie',
                    name: 'nom',
                    detectionType: 'type de détection',
                }
            },
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
                    color: 'Couleur'
                }
            },
            Category:{
                listing:{
                    add: 'Ajouter catégorie',
                    edit: 'Editer catégorie',
                },
                fields:{
                    name: 'Nom',
                    budget: 'Budget',
                    envelope: 'Enveloppe',
                }
            },
            Movement:{
                listing:{
                    add: 'Ajouter Mouvement',
                    edit: 'Editer Mouvement',
                },
                fields:{
                    amount: 'Montant',
                    comment: 'Commentaire',
                    label: 'Libellé',
                    category: 'Catégorie',
                    detectionMask: 'Masque de détection',
                }
            }
        }
    },
};

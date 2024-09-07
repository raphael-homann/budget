import Entity from "@efrogg/synergy/Data/Entity";
import Category from "./Category";
// --imports--  ! keep this line

export default class Budget extends Entity  {

    public name: string = '';
    public description: string = '';
    // ---properties---  ! keep this line

    // ---methods--- ! keep this line

    public get categories(): Category[] {
        return this.getOneToMany(Category, 'budgetId');
    }

    set categories(value: Category[]) {
        console.error('categories is a read only property');
    }
}

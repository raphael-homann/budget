import Entity from "@efrogg/synergy/Data/Entity";
import Budget from "./Budget";
// --imports--  ! keep this line

export default class Envelope extends Entity  {

    public static colorMapping = [
        {id: 'danger', title: 'Danger', color: 'deep-orange-darken-2'},
        {id: 'warning', title: 'Attention', color: 'orange-darken-2'},
        {id: 'success', title: 'Succès', color: 'green-darken-2'},
    ];

    public name: string = '';
    private _budget: Budget | null = null;
    private _budgetId: number | null = null;
    public color: string = '';
    // ---properties---  ! keep this line

    public get budget(): Budget | null {
        console.log('get budget' + this.budgetId);
        return this._budget ??= this.getRelation(Budget,this.budgetId);
    }

    public get budgetId(): number | null {
        return this._budgetId;
    }

    public set budgetId(value: number | null) {
        this._budgetId = value;
        this._budget = null;
    }

    // ---methods--- ! keep this line

    // custom methods
    public get finalColor(): string {
        const mapping = Envelope.colorMapping.find((item) => item.id === this.color);
        return mapping ? mapping.color : '';
    }
}

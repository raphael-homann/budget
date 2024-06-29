export default {
    methods: {
        getRouteParam: function ($route: any, paramName: string): string | null {
            return $route.params[paramName] ?? null;
        },
        getRouteParamInt: function ($route: any, paramName: string): number | null {
            let value = this.getRouteParam($route, paramName);
            if (value === null) {
                return null;
            }
            return parseInt(value);
        }
    }
}

import factory from "./factory";

class Container {
    constructor(factory) {
        this.factory = factory;
        this.components = {};
    }

    get(name) {
        if (!this.made(name)) {
            this.components[name] = this.make(name);
        }
        return this.components[name];
    }

    made(name) {
        return this.components.hasOwnProperty(name);
    }

    make(name, params = []) {
        return this.factory[name](this, params);
    }
}

export default new Container(factory);
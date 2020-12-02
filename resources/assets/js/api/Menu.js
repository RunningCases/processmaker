import menuData from "./../mocks/menu.json";

export let menu = {
    get() {
        return new Promise((resolutionFunc, rejectionFunc) => {
            resolutionFunc(menuData);
        });
    },
};

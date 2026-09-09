window.BuilderCanvasFocus = {
    bound: false,
    bind() {
        if (this.bound) return;
        this.bound = true;
        document.addEventListener('click', event => {
            if (BuilderInteractionBoundaries.preservesSelection(event.target)) return;
            BuilderSelectionManager.clear();
        });
    }
};

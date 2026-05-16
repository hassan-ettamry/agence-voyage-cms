window.BuilderDebugSnapshot = {

    capture(label = 'SNAPSHOT') {

        BuilderLogger.group(label);

        BuilderLogger.log(
            'STRUCTURE',
            structuredClone(
                BuilderStore.structure
            )
        );

        BuilderLogger.log(
            'SELECTED NODE',
            BuilderStore.selectedNodeId
        );

        BuilderLogger.log(
            'HISTORY STACK',
            BuilderHistory.stack.length
        );

        BuilderLogger.log(
            'FUTURE STACK',
            BuilderHistory.future.length
        );

        BuilderLogger.end();

    }

};
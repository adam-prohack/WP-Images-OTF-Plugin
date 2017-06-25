"use strict";

let gulp = require("gulp");
let config = require("./config.json");

let directories = {
    src: "src/",
    dest: config.outputDir || "dist/"
}

gulp.task("compile", function () {
    gulp.src(directories.src + "/**/*")
        .pipe(gulp.dest(directories.dest));
});

gulp.task("watch", function () {
    gulp.watch(directories.src + "/**/*", ["compile"]);
});
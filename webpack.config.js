const { resolve } = require("path");
const TerserJSPlugin = require("terser-webpack-plugin");

const devMode = process.env.NODE_ENV !== "production";

module.exports = {
  entry: "./src/index.js",
  mode: devMode ? "development" : "production",
  output: {
    path: resolve(__dirname, "dist"),
    filename: "laravel-crud.bundle.js",
  },
  plugins: [],
  module: {
    rules: [],
  },
  optimization: {
    minimizer: [
      new TerserJSPlugin({
        sourceMap: true,
      }),
    ],
  },
};

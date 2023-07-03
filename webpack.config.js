const webpack = require("webpack");
const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

const config = {
  entry: {
    main: ["./src/js/index.js", "./src/sass/main.scss"],
  },
  output: {
    path: path.resolve(__dirname, "dist"),
    filename: "[name].min.js",
    clean: true,
  },
  devtool: "source-map",
  module: {
    rules: [
      {
        test: /\.js$/,
        use: "babel-loader",
        exclude: /node_modules/,
      },
      {
        test: /\.css$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              importLoaders: 1,
            },
          },
          "postcss-loader",
        ],
      },
      {
        test: /\.scss$/,
        use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
      },
      {
        test: /\.(png|jpg|gif|svg)$/,
        type: "asset/resource",
        generator: {
          filename: "./img/[name][ext]",
        },
      },
    ],
  },
  plugins: [new MiniCssExtractPlugin()],
};

module.exports = config;

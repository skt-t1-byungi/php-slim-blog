require('dotenv').config();

const path = require('path');
const ExtractTextPlugin = require("extract-text-webpack-plugin");
// const CleanWebpackPlugin = require('clean-webpack-plugin');
const env = process.env;

module.exports = {
    entry: {
        app: [
            './resource/js/index.js',
            './resource/scss/style.scss',
        ],
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'public/assets/')
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: ExtractTextPlugin.extract({
                    use: ["css-loader", "sass-loader"],
                })
            },
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        "presets": [
                            ["env", {
                                "targets": {
                                    "browsers": ["last 2 versions"]
                                }
                            }]
                        ]
                    }
                }
            }
        ]
    },
    plugins: [
        // new CleanWebpackPlugin(['public/assets/'], {
        //     root: __dirname,
        //     verbose: true,
        //     dry: false,
        //     watch: false,
        //     exclude: ['files', 'to', 'ignore']
        // }),
        new ExtractTextPlugin("[name].css")
    ],
    devServer: {
        contentBase: __dirname,
        watchContentBase: true,
        compress: true,
        port: 8002,
        proxy: {
            "/": "http://127.0.0.1:" + env.WEB_PORT
        },
        publicPath: "/assets/"
    },
    devtool: env.DEV_MODE == 1 ? 'source-map' : false
};

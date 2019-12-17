const path = require('path');
const webpack = require('webpack');
const htmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
    mode: 'development',
    entry: './src/cliente/js/index.js',
    output: {
        path: path.join(__dirname, 'dist'),
        filename: 'bundle.js'
    },
    module:{
        rules:[
            {
                test:/\.css$/,
                use:['style-loader','css-loader']
            }
        ]
    },
    plugins:[
        new htmlWebpackPlugin({
            filename: 'index.html',
            template:'./src/cliente/index.html',
            chunks: ['main']
        }),
        new htmlWebpackPlugin({
            filename: 'contacto.html',
            template:'./src/cliente/contacto.html',
            chunks: ['exampleEntry']
        })
    ]
};
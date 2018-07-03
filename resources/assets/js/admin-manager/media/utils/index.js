import Images from './image-ext';

export default class MediaUtils{
    static isImage(fileName){
        return !!_.some(Images, (i)=>_.endsWith(fileName.toLowerCase() , i));
    }

    static isImageByExt(ext){
        return !!_.some(Images, (i)=> i.toLowerCase() === ext);
    }

    static randomInteger(min, max) {
        let rand = min - 0.5 + Math.random() * (max - min + 1);

        rand = Math.round(rand);

        return rand;
    }

};
